<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AnalyticsRateLimiter
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next)
    {
        $key = 'analytics:' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, 60)) {
            return response()->json([
                'error' => 'Too many requests',
                'retry_after' => $this->limiter->availableIn($key)
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($key);

        return $next($request);
    }
} 