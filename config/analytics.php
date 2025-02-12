<?php

return [
    'privacy' => [
        'anonymize_ip' => true,
        'respect_dnt' => true,
        'cookie_consent' => true,
        'data_retention' => 90, // days
    ],
    
    'tracking' => [
        'excluded_ips' => [],
        'excluded_paths' => [],
        'excluded_user_agents' => []
    ],
    
    'sampling' => [
        'enabled' => false,
        'rate' => 100
    ]
]; 