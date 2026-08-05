<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ZABIDA Facebook Page sync — see .env.example / setup guide Phase 1.6
    // for how to obtain a long-lived Page Access Token.
// ZABIDA Facebook Page sync — see .env.example / setup guide Phase 1.6
    // for how to obtain a long-lived Page Access Token.
    'facebook' => [
        'page_id' => env('FACEBOOK_PAGE_ID'),
        'page_token' => env('FACEBOOK_PAGE_TOKEN'),
        'user_token' => env('FACEBOOK_USER_TOKEN', env('FACEBOOK_PAGE_TOKEN')),
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
    ],

];
