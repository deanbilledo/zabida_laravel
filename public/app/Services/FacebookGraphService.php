<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Pulls posts from the ZABIDA Facebook Page via Graph API and turns them
 * into local Post + PostImage records — including full photo albums
 * (reposts with multiple images) and native video, not just the first
 * image of a post.
 *
 * Requires a Page Access Token with pages_show_list, pages_read_engagement,
 * and pages_read_user_content (already granted per the project brief).
 */
class FacebookGraphService
{
    protected string $baseUrl = 'https://graph.facebook.com/v19.0';
    protected string $pageId;
    protected string $token;

    public function __construct()
    {
        $this->pageId = (string) config('services.facebook.page_id');
        $this->token = (string) config('services.facebook.page_token');
    }

    public function isConfigured(): bool
    {
        return $this->pageId !== '' && $this->token !== '';
    }

    /**
     * Fetch recent page posts (including reposts/shares) with full
     * attachment expansion, so multi-photo albums and video sources come
     * back in a single call instead of one request per photo.
     *
     * @return array<int, array>
     */
    public function fetchRecentPosts(int $limit = 25): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Facebook is not configured. Set FACEBOOK_PAGE_ID and FACEBOOK_PAGE_TOKEN in .env.');
        }

        $fields = implode(',', [
            'id',
            'message',
            'created_time',
            'permalink_url',
            // attachments{} expansion is what surfaces every photo in an
            // album (via subattachments) and the playable video URL
            // (media.source on a video_inline attachment), in one request.
            'attachments{media_type,media,url,title,subattachments{media_type,media,url}}',
        ]);

        // /{page-id}/posts throws error #100 ("missing permission ... Page
        // Public Content Access") for almost everyone unless the app has
        // gone through App Review for that feature — even with the right
        // permissions on the token. /{page-id}/feed returns the same
        // content (everything posted to/by the Page) and works with a
        // standard Page Access Token that has pages_read_engagement, which
        // is exactly what you already have.
        $response = Http::get("{$this->baseUrl}/{$this->pageId}/feed", [
            'fields' => $fields,
            'limit' => $limit,
            'access_token' => $this->token,
        ]);

        if ($response->failed()) {
            Log::error('ZABIDA Facebook sync: Graph API request failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);
            throw new RuntimeException('Facebook Graph API request failed: '.$this->extractErrorMessage($response));
        }

        return $response->json('data', []);
    }

    protected function extractErrorMessage($response): string
    {
        $error = $response->json('error');
        return is_array($error) ? ($error['message'] ?? 'Unknown error') : 'Unknown error';
    }

    /**
     * Import posts that don't exist locally yet. Returns the number of
     * posts created. Safe to call repeatedly — already-imported posts
     * (matched by facebook_post_id) are skipped.
     */
    public function syncNewPosts(int $limit = 25): int
    {
        $created = 0;

        foreach ($this->fetchRecentPosts($limit) as $fbPost) {
            if (empty($fbPost['id'])) {
                continue;
            }

            if (Post::where('facebook_post_id', $fbPost['id'])->exists()) {
                continue; // already imported
            }

            $message = trim($fbPost['message'] ?? '');
            if ($message === '' && empty($fbPost['attachments'])) {
                continue; // nothing worth showing (e.g. a bare status change)
            }

            $title = Str::limit(Str::of($message)->explode("\n")->first() ?: 'Facebook update', 80);
            $excerpt = Str::limit($message, 240);

            $post = Post::create([
                'title' => $title ?: 'Facebook update',
                'excerpt' => $excerpt,
                'body' => $message,
                'source' => 'facebook',
                'facebook_post_id' => $fbPost['id'],
                'facebook_permalink' => $fbPost['permalink_url'] ?? null,
                'published_at' => isset($fbPost['created_time'])
                    ? date('Y-m-d', strtotime($fbPost['created_time']))
                    : now()->toDateString(),
            ]);

            $this->importAttachments($post, $fbPost['attachments']['data'] ?? []);

            $created++;
        }

        return $created;
    }

    /**
     * Walk every attachment on a post — including subattachments, which is
     * how Facebook represents a multi-photo album — download each photo
     * locally, and capture a video source URL if present.
     */
    protected function importAttachments(Post $post, array $attachments): void
    {
        $position = 0;
        $coverSet = false;

        foreach ($attachments as $attachment) {
            $mediaType = $attachment['media_type'] ?? null;

            // Multi-photo album: Facebook nests the individual photos here.
            if (! empty($attachment['subattachments']['data'])) {
                foreach ($attachment['subattachments']['data'] as $sub) {
                    $position = $this->importOnePhoto($post, $sub, $position, $coverSet);
                    $coverSet = true;
                }
                continue;
            }

            if ($mediaType === 'photo') {
                $position = $this->importOnePhoto($post, $attachment, $position, $coverSet);
                $coverSet = true;
            } elseif (in_array($mediaType, ['video_inline', 'video_autoplay', 'video_share'], true)) {
                // media.source on a video attachment is a direct, playable
                // MP4 URL — that's what makes the video actually work in
                // an HTML5 <video> tag rather than just linking out to FB.
                $source = $attachment['media']['source'] ?? null;
                if ($source) {
                    $post->video_url = $source;
                    $post->save();
                }
            }
        }
    }

    protected function importOnePhoto(Post $post, array $attachment, int $position, bool $coverAlreadySet): int
    {
        $imageUrl = $attachment['media']['image']['src'] ?? null;
        if (! $imageUrl) {
            return $position;
        }

        $localPath = $this->downloadImage($imageUrl, $post->id, $position);
        if (! $localPath) {
            return $position;
        }

        PostImage::create([
            'post_id' => $post->id,
            'path' => $localPath,
            'facebook_media_id' => $attachment['target']['id'] ?? null,
            'position' => $position,
        ]);

        if (! $coverAlreadySet) {
            $post->image = $localPath;
            $post->save();
        }

        return $position + 1;
    }

    /**
     * Facebook's CDN photo URLs expire after a while, so every photo is
     * downloaded once and stored on our own disk — this is what keeps
     * the gallery working long after the original post's URL has gone stale.
     */
    protected function downloadImage(string $url, int $postId, int $position): ?string
    {
        try {
            $response = Http::timeout(20)->get($url);
            if ($response->failed()) {
                return null;
            }

            $extension = 'jpg';
            $contentType = $response->header('Content-Type');
            if ($contentType === 'image/png') {
                $extension = 'png';
            }

            $path = "posts/{$postId}/".Str::random(12).'-'.$position.'.'.$extension;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $e) {
            Log::warning('ZABIDA Facebook sync: image download failed', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
