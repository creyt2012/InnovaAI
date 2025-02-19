<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SystemConfig;
use Illuminate\Support\Facades\Cache;

class SystemConfigServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load cấu hình từ cache hoặc database
        $configs = Cache::tags('system_config')->remember('system_configs', 3600, function () {
            return SystemConfig::where('is_public', true)
                             ->pluck('value', 'key')
                             ->toArray();
        });

        config(['system' => $configs]);
    }
} 