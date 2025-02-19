<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemConfig;

class SystemConfigSeeder extends Seeder
{
    public function run()
    {
        $configs = [
            [
                'key' => 'site_name',
                'value' => 'InnovaAI',
                'group' => 'general',
                'type' => 'text',
                'is_public' => true
            ],
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'group' => 'system',
                'type' => 'boolean',
                'is_public' => true
            ],
            [
                'key' => 'chat_max_tokens',
                'value' => 1000,
                'group' => 'chat',
                'type' => 'number',
                'is_public' => true
            ]
        ];

        foreach ($configs as $config) {
            SystemConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
} 