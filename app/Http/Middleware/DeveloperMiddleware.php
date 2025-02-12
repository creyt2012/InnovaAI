<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DeveloperMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->developer || $request->user()->developer->status !== 'verified') {
            return redirect()->route('developer.register');
        }

        return $next($request);
    }
} 