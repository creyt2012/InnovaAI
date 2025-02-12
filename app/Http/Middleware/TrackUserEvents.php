<?php

namespace App\Http\Middleware;

use Closure;
use App\Events\UserEvent;

class TrackUserEvents
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->ajax() || $request->wantsJson()) {
            return $response;
        }

        // Track page view
        event(new UserEvent('pageview', [
            'url' => $request->fullUrl(),
            'referrer' => $request->header('referer')
        ]));

        return $response;
    }
} 