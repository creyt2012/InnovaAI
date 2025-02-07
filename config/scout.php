<?php

return [
    'driver' => env('SCOUT_DRIVER', 'meilisearch'),

    'queue' => true,

    'after_commit' => false,

    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],

    'soft_delete' => false,

    'identify' => env('SCOUT_IDENTIFY', false),

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            'chats' => [
                'filterableAttributes' => ['user_id', 'created_at', 'status'],
                'sortableAttributes' => ['created_at'],
            ],
        ],
    ],
]; 