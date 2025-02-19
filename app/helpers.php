<?php

if (!function_exists('system_config')) {
    function system_config($key, $default = null)
    {
        return \App\Models\SystemConfig::get($key, $default);
    }
} 