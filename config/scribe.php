<?php

return [
    'type' => 'static',
    'static' => [
        'output_path' => 'public/docs',
    ],
    
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
            ],
            'include' => ['*'],
            'exclude' => [
                // Routes to exclude from documentation
            ],
        ],
    ],

    'title' => 'LM Studio API Documentation',
    'description' => 'API documentation for LM Studio chat application',
    
    'auth' => [
        'enabled' => true,
        'default' => false,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => 'Bearer {token}',
        'placeholder' => '{token}',
        'extra_info' => 'You can get a token by logging in via POST /api/login',
    ],

    'examples' => [
        'faker_seed' => 12345,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'groups' => [
        'order' => [
            'Authentication',
            'Chat API',
            'Server Management API',
        ],
    ],
]; 