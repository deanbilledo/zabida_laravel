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
}
