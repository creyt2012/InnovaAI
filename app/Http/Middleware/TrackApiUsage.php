<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class TrackApiUsage
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Track API usage
        Redis::hincrby(
            'api_usage:' . date('Y-m-d'),
            $request->path(),
            1
        );

        return $response;
    }
} 