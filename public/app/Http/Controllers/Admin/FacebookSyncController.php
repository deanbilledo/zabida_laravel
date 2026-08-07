<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookSyncLog;
use App\Services\FacebookGraphService;
use Illuminate\Http\RedirectResponse;

class FacebookSyncController extends Controller
{
    public function index(FacebookGraphService $facebook)
    {
        return view('admin.facebook-sync', [
            'isConfigured' => $facebook->isConfigured(),
            'logs' => FacebookSyncLog::orderByDesc('ran_at')->take(20)->get(),
        ]);
    }

    // Manual "Sync now" button — gives visible success/error feedback
    // instead of leaving the admin guessing whether anything happened.
    public function sync(FacebookGraphService $facebook): RedirectResponse
    {
        if (! $facebook->isConfigured()) {
            return back()
                ->with('status', 'error')
                ->with('message', 'Facebook isn\'t configured yet — set FACEBOOK_PAGE_ID and FACEBOOK_PAGE_TOKEN in .env first.');
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
            $message = $e->getMessage();

            // Facebook's expired/invalid token errors are distinctive enough to
            // detect and turn into an actionable message instead of raw JSON.
            if (str_contains($message, 'Session has expired') || str_contains($message, 'Error validating access token')) {
                $message = 'Your Facebook access token has expired. Go to the settings below and paste in a fresh token to fix this.';
            }

            FacebookSyncLog::create([
                'status' => 'error',
                'posts_created' => 0,
                'message' => $message,
                'ran_at' => now(),
            ]);

            return back()
                ->with('status', 'error')
                ->with('message', 'Sync failed: '.$message);
        }
    }
}
