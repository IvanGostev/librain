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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'vkontakte' => [
        'client_id' => env('VK_CLIENT_ID', '52900463'),
        'client_secret' => env('VK_CLIENT_SECRET', 'xxrBjDrsOqUudtbdRKvm'),
        'redirect' => env('VK_REDIRECT_URI', 'http://localhost:8000/auth/vkontakte/callback'),
    ],

    'odnoklassniki' => [
        'client_id' => env('OK_CLIENT_ID', '512003243521'),
        'client_public' => env('OK_CLIENT_PUBLIC', 'CCAPHGMGDIHBABABA'),
        'client_secret' => env('OK_CLIENT_SECRET', 'D4E3BF342FEF42DD9A509CDC'),
        'redirect' => env('OK_REDIRECT_URI', 'http://localhost:8000/auth/odnoklassniki/callback'),
    ],

    'telegram' => [
        'bot' => env('TELEGRAM_BOT_NAME'),
        'client_id' => env('TELEGRAM_BOT_NAME'),
        'client_secret' => env('TELEGRAM_TOKEN'),
        'redirect' => env('TELEGRAM_REDIRECT_URI'),
    ],

];
