<?php

use Illuminate\Support\Str;

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        // Public-facing images: post photos, cover images, publication
        // thumbnails. Linked into public/storage via `php artisan storage:link`.
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // PDF archive storage — deliberately NOT under public/ or the
        // 'public' disk above. Every PDF is only reachable through the
        // authenticated view()/download() controller routes, which is what
        // keeps this "safe and secured" rather than every file having a
        // guessable, directly-linkable URL.
        'publications' => [
            'driver' => 'local',
            'root' => storage_path('app/publications'),
            'visibility' => 'private',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
