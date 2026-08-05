<?php

namespace App\Console\Commands;

use App\Models\FacebookSyncLog;
use App\Services\FacebookGraphService;
use Illuminate\Console\Command;

class SyncFacebookPosts extends Command
{
    protected $signature = 'facebook:sync';
    protected $description = 'Import new posts (with photo albums and video) from the ZABIDA Facebook Page';

    public function handle(FacebookGraphService $facebook): int
    {
        if (! $facebook->isConfigured()) {
            $this->error('Facebook is not configured. Set FACEBOOK_PAGE_ID and FACEBOOK_PAGE_TOKEN in .env.');
            return self::FAILURE;
        }

        try {
            $created = $facebook->syncNewPosts();

            FacebookSyncLog::create([
                'status' => 'success',
                'posts_created' => $created,
                'message' => "Imported {$created} new post(s).",
                'ran_at' => now(),
            ]);

            $this->info("Sync complete — imported {$created} new post(s).");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            FacebookSyncLog::create([
                'status' => 'error',
                'posts_created' => 0,
                'message' => $e->getMessage(),
                'ran_at' => now(),
            ]);

            $this->error('Sync failed: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
