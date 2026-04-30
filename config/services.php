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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
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

    'sentiment' => [
        'enabled' => env('SENTIMENT_SERVICE_ENABLED', true),
        'url' => env('SENTIMENT_API_URL', 'http://127.0.0.1:5000'),
        'timeout' => env('SENTIMENT_API_TIMEOUT', 30),
        'batch_timeout' => env('SENTIMENT_API_BATCH_TIMEOUT', 90),
        'auto_save' => env('SENTIMENT_AUTO_SAVE', true),
    ],
    
    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'go_backend' => [
        'url' => env('GO_BACKEND_URL', 'http://localhost:8080'),
    ],

];
