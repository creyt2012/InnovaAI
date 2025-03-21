<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AIRateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'ai_' . ($request->user() ? $request->user()->id : $request->ip());
        
        if (RateLimiter::tooManyAttempts($key, config('ai.rate_limits.requests_per_minute'))) {
            return response()->json([
                'message' => 'Too many requests',
                'retry_after' => RateLimiter::availableIn($key)
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($key);

        return $next($request);
    }
} 