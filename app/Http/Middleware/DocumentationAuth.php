<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DocumentationAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!app()->environment('local') && !auth()->check()) {
            abort(403, 'Documentation access denied');
        }

        return $next($request);
    }
} 