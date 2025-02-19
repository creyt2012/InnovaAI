<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function remember($key, $callback, $ttl = 3600)
    {
        return Cache::tags(['chat'])->remember($key, $ttl, $callback);
    }

    public function invalidateUserCache($userId)
    {
        Cache::tags(['user:' . $userId, 'chat'])->flush();
    }

    public function warmup($userId)
    {
        // Preload commonly accessed data
        $this->remember("user.{$userId}.preferences", function() use ($userId) {
            return UserPreference::find($userId);
        });
    }
} 