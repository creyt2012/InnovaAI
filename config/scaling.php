<?php

return [
    'min_instances' => env('SCALING_MIN_INSTANCES', 1),
    'max_instances' => env('SCALING_MAX_INSTANCES', 5),
    'scale_up_threshold' => env('SCALING_UP_THRESHOLD', 80),
    'scale_down_threshold' => env('SCALING_DOWN_THRESHOLD', 30),
    'cooldown_period' => env('SCALING_COOLDOWN_PERIOD', 300),
    
    'provisioner_url' => env('SCALING_PROVISIONER_URL'),
    
    'server_template' => [
        'host' => env('SCALING_SERVER_HOST', 'localhost'),
        'configuration' => [
            'model' => env('LM_STUDIO_MODEL', 'gpt-3.5-turbo'),
            'max_tokens' => env('LM_STUDIO_MAX_TOKENS', 2000),
            'temperature' => env('LM_STUDIO_TEMPERATURE', 0.7),
        ],
    ],
]; 