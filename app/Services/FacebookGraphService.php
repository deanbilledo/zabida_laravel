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
            'attachments{type,media_type,media{image{src},source},target,subattachments{type,media_type,media{image{src},source},target}}',
        ]);

        $response = Http::timeout(30)->retry(2, 500)->get("{$this->baseUrl}/me/posts", [
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
     */
    public function syncNewPosts(?int $limit = null): int
    {
        set_time_limit(0);

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
                continue;
            }

            $message = trim($fbPost['message'] ?? '');
            $attachments = $fbPost['attachments']['data'] ?? [];
            $fullPicture = $fbPost['full_picture'] ?? null;

            if ($message === '' && empty($attachments) && empty($fullPicture)) {
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

            $importedCount = 0;
            if (! empty($attachments)) {
                $importedCount = $this->importAttachments($post, $attachments);
            }

            if ($importedCount === 0 && ! empty($fullPicture)) {
                $this->importSharedPicture($post, $fullPicture);
                $importedCount = 1;
            }

            // Last-resort fallback — query this specific post directly for
            // full_picture/picture. Covers native_templates / share types
            // where the batched /me/posts response omits image data entirely.
            // If this also comes back empty, the post has no resolvable
            // image (often because it's actually a video share) — the
            // Blade view shows a "watch on Facebook" prompt in that case.
            if ($importedCount === 0) {
                $resolvedUrl = $this->resolvePostPicture($fbPostId);
                if ($resolvedUrl) {
                    $this->importSharedPicture($post, $resolvedUrl);
                    $importedCount = 1;
                } else {
                    Log::info('ZABIDA Facebook sync: no image found for post', [
                        'facebook_post_id' => $fbPostId,
                    ]);
                }
            }

            $created++;
        }

        return $created;
    }

    protected function resolvePostPicture(string $postId): ?string
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$postId}", [
                'fields' => 'full_picture,picture',
                'access_token' => $this->userToken,
            ]);

            if ($response->failed()) {
                Log::warning('ZABIDA Facebook sync: direct post picture lookup failed', [
                    'post_id' => $postId,
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->json('full_picture') ?? $response->json('picture');
        } catch (\Throwable $e) {
            Log::warning('ZABIDA Facebook sync: direct post picture lookup exception', [
                'post_id' => $postId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Parse attachments and subattachments. Returns count of imported images.
     */
    protected function importAttachments(Post $post, array $attachments): int
    {
        $position = 0;
        $coverSet = false;

        foreach ($attachments as $attachment) {
            $mediaType = $attachment['media_type'] ?? $attachment['type'] ?? null;
            $type = $attachment['type'] ?? null;

            // Confirmed video attachment — Graph API explicitly labels this
            // as video, so we can trust it and set video_url directly.
            if (in_array($mediaType, ['video_inline', 'video_autoplay', 'video_share'], true)) {
                $post->video_url = $post->facebook_permalink;
                $post->save();
                continue;
            }

            // native_templates with no target/media at all — Graph API gives
            // us nothing to reliably tell video from photo/link shares here.
            // We leave this post with no image/video; the Blade view shows
            // a "watch on Facebook" prompt for any Facebook post with no
            // resolvable media, which correctly covers this case too.
            if ($type === 'native_templates' && empty($attachment['target']) && empty($attachment['media'])) {
                continue;
            }

            $newPosition = $this->importOnePhoto($post, $attachment, $position, $coverSet);
            if ($newPosition > $position) {
                $position = $newPosition;
                $coverSet = true;
            }

            if (! empty($attachment['subattachments']['data'])) {
                foreach ($attachment['subattachments']['data'] as $sub) {
                    $newPosition = $this->importOnePhoto($post, $sub, $position, $coverSet);
                    if ($newPosition > $position) {
                        $position = $newPosition;
                        $coverSet = true;
                    }
                }
            }
        }

        return $position;
    }

    protected function importOnePhoto(Post $post, array $attachment, int $position, bool $coverAlreadySet): int
    {
        $mediaType = $attachment['media_type'] ?? $attachment['type'] ?? null;
        if (in_array($mediaType, ['video_inline', 'video_autoplay', 'video_share'], true)) {
            return $position; // video frame, not a real photo — handled separately
        }

        $imageUrl = $attachment['media']['image']['src']
            ?? $attachment['media']['src']
            ?? null;

        // Shared/repost attachments (photo type with a target id but no
        // direct media) — resolve the original photo via its target id.
        if (! $imageUrl && ! empty($attachment['target']['id'])) {
            $imageUrl = $this->resolveSharedImage($attachment['target']['id']);
        }

        if (! $imageUrl) {
            return $position;
        }

        $mediaId = $attachment['target']['id'] ?? null;
        if ($mediaId && PostImage::where('post_id', $post->id)->where('facebook_media_id', $mediaId)->exists()) {
            return $position;
        }

        $localPath = $this->downloadImage($imageUrl, $post->id, $position);
        if (! $localPath) {
            return $position;
        }

        PostImage::create([
            'post_id' => $post->id,
            'path' => $localPath,
            'facebook_media_id' => $mediaId,
            'position' => $position,
        ]);

        if (! $coverAlreadySet) {
            $post::where('id', $post->id)->update(['image' => $localPath]);
        }

        return $position + 1;
    }

    /**
     * For shared posts, the attachment only gives us a target id (the original
     * post/page/photo being shared). Query that object directly for its picture.
     */
    protected function resolveSharedImage(string $targetId): ?string
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$targetId}", [
                'fields' => 'full_picture',
                'access_token' => $this->userToken,
            ]);

            if ($response->failed()) {
                Log::warning('ZABIDA Facebook sync: failed to resolve shared image', [
                    'target_id' => $targetId,
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->json('full_picture');
        } catch (\Throwable $e) {
            Log::warning('ZABIDA Facebook sync: shared image lookup exception', [
                'target_id' => $targetId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Fallback for shared posts or links that have a preview image.
     */
    protected function importSharedPicture(Post $post, string $imageUrl): void
    {
        $localPath = $this->downloadImage($imageUrl, $post->id, 0);

        if ($localPath) {
            $post::where('id', $post->id)->update(['image' => $localPath]);

            PostImage::create([
                'post_id' => $post->id,
                'path' => $localPath,
                'position' => 0,
            ]);
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