<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class AnalyticsCacheService
{
    protected $ttl = 3600; // 1 hour

    public function remember($key, $callback)
    {
        return Cache::tags(['analytics'])->remember($key, $this->ttl, $callback);
    }

    public function flush()
    {
        Cache::tags(['analytics'])->flush();
    }

    public function setTTL($seconds)
    {
        $this->ttl = $seconds;
        return $this;
    }
} 