<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ChatRateLimiter
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle($request, Closure $next)
    {
        $key = 'chat:' . $request->user()->id;

        if ($this->limiter->tooManyAttempts($key, 60)) {
            return response()->json([
                'message' => 'Too many requests'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($key);

        return $next($request);
    }
} 