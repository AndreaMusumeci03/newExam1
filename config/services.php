<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

  'tmdb' => [
        'token'  => env('TMDB_TOKEN'),
        'lang'   => env('TMDB_LANG', 'it-IT'),
        // Se specificato, path al cacert.pem per cURL (Windows/XAMPP)
        'ca'     => env('TMDB_CA_PATH', null),
        // Fallback booleano per abilitare/disabilitare la verifica SSL
        // Imposta TMDB_VERIFY_SSL=false SOLO in locale per debug.
        'verify' => env('TMDB_VERIFY_SSL', true),
    ],
];
