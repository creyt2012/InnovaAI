<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SystemConfig;

class CheckMaintenanceMode
{
    public function handle($request, Closure $next)
    {
        if (SystemConfig::get('maintenance_mode', false) && !$request->user()?->isAdmin()) {
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
} 