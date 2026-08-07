<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookSyncLog;
use App\Services\FacebookGraphService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class FacebookSyncController extends Controller
{
    public function index(FacebookGraphService $facebook)
    {
        return view('admin.facebook-sync', [
            'isConfigured' => $facebook->isConfigured(),
            'logs' => FacebookSyncLog::orderByDesc('ran_at')->take(20)->get(),
            'currentPageId' => config('services.facebook.page_id'),
            'currentTokenMasked' => $this->maskSecret(config('services.facebook.page_token')),
            'currentAppId' => config('services.facebook.app_id'),
            'currentAppSecretMasked' => $this->maskSecret(config('services.facebook.app_secret')),
        ]);
    }

    // Manual "Sync now" button — gives visible success/error feedback
    // instead of leaving the admin guessing whether anything happened.
    public function sync(FacebookGraphService $facebook): RedirectResponse
    {
        if (! $facebook->isConfigured()) {
            return back()
                ->with('status', 'error')
                ->with('message', 'Facebook isn\'t configured yet — set it up below first.');
        }

        try {
            $created = $facebook->syncNewPosts();

            FacebookSyncLog::create([
                'status' => 'success',
                'posts_created' => $created,
                'message' => $created > 0
                    ? "Imported {$created} new post(s)."
                    : 'No new posts found — everything is already up to date.',
                'ran_at' => now(),
            ]);

            return back()
                ->with('status', 'success')
                ->with('message', $created > 0
                    ? "Sync complete — imported {$created} new post(s), including any photo albums and video."
                    : 'Sync complete — no new posts to import.');
        } catch (\Throwable $e) {
            FacebookSyncLog::create([
                'status' => 'error',
                'posts_created' => 0,
                'message' => $e->getMessage(),
                'ran_at' => now(),
            ]);

            return back()
                ->with('status', 'error')
                ->with('message', 'Sync failed: '.$e->getMessage());
        }
    }

    /**
     * Update Facebook credentials directly in .env (route is super_admin
     * only). The Page token is verified against Graph API before anything
     * is written, so a bad/malformed token never gets saved.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_id' => ['required', 'string'],
            'page_token' => ['required', 'string'],
            'app_id' => ['nullable', 'string'],
            'app_secret' => ['nullable', 'string'],
        ]);

        $pageId = trim($validated['page_id']);
        $pageToken = trim($validated['page_token']);

        // Verify the token actually works before touching .env at all.
        $test = Http::get("https://graph.facebook.com/v26.0/{$pageId}", [
            'fields' => 'id,name',
            'access_token' => $pageToken,
        ]);

        if ($test->failed()) {
            $error = $test->json('error.message') ?? 'Unknown error from Facebook.';

            return back()
                ->with('status', 'error')
                ->with('message', "Couldn't verify that token — nothing was saved. Facebook said: {$error}");
        }

        $envPath = base_path('.env');

        if (! File::exists($envPath) || ! File::isWritable($envPath)) {
            return back()
                ->with('status', 'error')
                ->with('message', 'The .env file is missing or not writable by the server. Update it manually via your hosting file manager or SSH.');
        }

        $content = File::get($envPath);

        $content = $this->setEnvValue($content, 'FACEBOOK_PAGE_ID', $pageId);
        $content = $this->setEnvValue($content, 'FACEBOOK_PAGE_TOKEN', $pageToken);

        if (! empty($validated['app_id'])) {
            $content = $this->setEnvValue($content, 'FACEBOOK_APP_ID', trim($validated['app_id']));
        }
        if (! empty($validated['app_secret'])) {
            $content = $this->setEnvValue($content, 'FACEBOOK_APP_SECRET', trim($validated['app_secret']));
        }

        File::put($envPath, $content);

        Artisan::call('config:clear');

        return back()
            ->with('status', 'success')
            ->with('message', 'Facebook credentials verified and saved. Page name: '.$test->json('name'));
    }

    /**
     * Replace or append a KEY=value line in a raw .env file's contents.
     * Wraps the value in quotes if it contains spaces/special characters,
     * to avoid corrupting the file the way unquoted secrets sometimes can.
     */
    protected function setEnvValue(string $content, string $key, string $value): string
    {
        $escaped = str_contains($value, ' ') || str_contains($value, '#')
            ? '"'.str_replace('"', '\"', $value).'"'
            : $value;

        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, "{$key}={$escaped}", $content);
        }

        return rtrim($content)."\n{$key}={$escaped}\n";
    }

    protected function maskSecret(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (strlen($value) <= 10) {
            return str_repeat('•', strlen($value));
        }

        return substr($value, 0, 6).str_repeat('•', 8).substr($value, -4);
    }
}