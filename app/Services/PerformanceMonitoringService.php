<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringService
{
    public function trackApiResponse($endpoint, $duration)
    {
        Redis::zadd(
            'api_response_times:' . date('Y-m-d'),
            $duration,
            $endpoint . ':' . microtime(true)
        );

        if ($duration > 1000) { // > 1 second
            Log::warning("Slow API response: {$endpoint} took {$duration}ms");
        }
    }

    public function getAverageResponseTime($endpoint)
    {
        $times = Redis::zrangebyscore(
            'api_response_times:' . date('Y-m-d'),
            '-inf',
            '+inf',
            ['withscores' => true]
        );

        return array_sum($times) / count($times);
    }
} 