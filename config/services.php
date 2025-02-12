<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'lmstudio' => [
        'url' => env('LMSTUDIO_API_URL'),
        'endpoints' => [
            env('LMSTUDIO_API_URL'),
            env('LMSTUDIO_API_URL_2'),
            env('LMSTUDIO_API_URL_3'),
        ],
        'temperature' => env('LMSTUDIO_TEMPERATURE', 0.7),
        'max_tokens' => env('LMSTUDIO_MAX_TOKENS', 1000),
        'timeout' => env('LMSTUDIO_TIMEOUT', 30),
    ],
]; 