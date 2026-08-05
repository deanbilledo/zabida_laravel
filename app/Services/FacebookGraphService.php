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
 * into local Post + PostImage records.
 */
class FacebookGraphService
{
    protected string $baseUrl = 'https://graph.facebook.com/v26.0';
    protected string $pageId;
    protected string $userToken;

    public function __construct()
    {
        $this->pageId = (string) config('services.facebook.page_id');
        $this->userToken = (string) (config('services.facebook.user_token') ?? config('services.facebook.page_token'));
    }

    public function isConfigured(): bool
    {
        return $this->pageId !== '' && $this->userToken !== '';
    }

    /**
     * Fetch recent page posts using /me/posts with a Page Access Token.
     */
    public function fetchRecentPosts(int $limit = 5): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Facebook is not configured. Set FACEBOOK_PAGE_ID and FACEBOOK_PAGE_TOKEN in .env.');
        }

        $postFields = implode(',', [
            'id',
            'message',
            'created_time',
            'permalink_url',
            'full_picture',
            'attachments{media_type,media,url,title,subattachments{media_type,media,url}}',
        ]);

        $response = Http::get("{$this->baseUrl}/me/posts", [
            'fields' => $postFields,
            'limit' => $limit,
            'access_token' => $this->userToken,
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
     * Import posts that don't exist locally yet.
     * Automatically handles bulk initial sync vs daily light sync.
     */
    public function syncNewPosts(?int $limit = null): int
    {
        // 1. Prevent PHP timeout when downloading multiple images
        set_time_limit(0);

        // 2. Smart Limit Logic: If empty DB, fetch 100 posts. If not empty, check latest 5.
        if ($limit === null) {
            $hasExistingPosts = Post::where('source', 'facebook')->exists();
            $limit = $hasExistingPosts ? 5 : 100;
        }

        $created = 0;
        $posts = $this->fetchRecentPosts($limit);

        foreach ($posts as $fbPost) {
            if (empty($fbPost['id'])) {
                continue;
            }

            $fbPostId = $fbPost['id'];

            if (Post::where('facebook_post_id', $fbPostId)->exists()) {
                continue; // already imported
            }

            $message = trim($fbPost['message'] ?? '');
            $attachments = $fbPost['attachments']['data'] ?? [];

            if ($message === '' && empty($attachments) && empty($fbPost['full_picture'])) {
                continue;
            }

            $title = Str::limit(Str::of($message)->explode("\n")->first() ?: 'Facebook update', 80);
            $excerpt = Str::limit($message, 240);

            $post = Post::create([
                'title' => $title ?: 'Facebook update',
                'excerpt' => $excerpt,
                'body' => $message,
                'source' => 'facebook',
                'facebook_post_id' => $fbPostId,
                'facebook_permalink' => $fbPost['permalink_url'] ?? null,
                'published_at' => isset($fbPost['created_time'])
                    ? date('Y-m-d H:i:s', strtotime($fbPost['created_time']))
                    : now(),
            ]);

            if (! empty($attachments)) {
                $this->importAttachments($post, $attachments);
            } elseif (! empty($fbPost['full_picture'])) {
                $this->importSharedPicture($post, $fbPost['full_picture']);
            }

            $created++;
        }

        return $created;
    }

    /**
     * Parse attachments and subattachments (albums, single photos, videos).
     */
    protected function importAttachments(Post $post, array $attachments): void
    {
        $position = 0;
        $coverSet = false;

        foreach ($attachments as $attachment) {
            // Multi-photo album (subattachments)
            if (! empty($attachment['subattachments']['data'])) {
                foreach ($attachment['subattachments']['data'] as $sub) {
                    $position = $this->importOnePhoto($post, $sub, $position, $coverSet);
                    $coverSet = true;
                }
                continue;
            }

            $mediaType = $attachment['media_type'] ?? null;

            if ($mediaType === 'photo' || isset($attachment['media']['image'])) {
                $position = $this->importOnePhoto($post, $attachment, $position, $coverSet);
                $coverSet = true;
            } elseif (in_array($mediaType, ['video_inline', 'video_autoplay', 'video_share'], true)) {
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
        $imageUrl = $attachment['media']['image']['src'] ?? $attachment['url'] ?? null;
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
            $post::where('id', $post->id)->update(['image' => $localPath]);
        }

        return $position + 1;
    }

    /**
     * Fallback for shared posts or links that have a preview image but no standard attachments.
     */
    protected function importSharedPicture(Post $post, string $imageUrl): void
    {
        $localPath = $this->downloadImage($imageUrl, $post->id, 0);

        if ($localPath) {
            $post::where('id', $post->id)->update(['image' => $localPath]);
        }
    }

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