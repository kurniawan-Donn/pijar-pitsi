<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    // Membaca jam dari foto kartu absensi (Revisi 6). Kunci API hanya di .env.
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        // gemini-2.5-flash sudah tidak tersedia untuk pengguna baru (Oktober 2026).
        'model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),
        // Sisi kartu yang cetakannya meleset bisa butuh 30–60 detik.
        'timeout' => (int) env('GEMINI_TIMEOUT', 120),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
