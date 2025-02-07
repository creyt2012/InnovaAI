<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

    'breadcrumbs' => [
        'logs' => true,
        'sql_queries' => true,
        'sql_bindings' => true,
        'queue_info' => true,
        'command_info' => true,
    ],

    'send_default_pii' => true,

    'traces_sample_rate' => (float)(env('SENTRY_TRACES_SAMPLE_RATE', 0.2)),
]; 