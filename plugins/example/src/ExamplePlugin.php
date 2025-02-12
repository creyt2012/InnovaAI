<?php

namespace Plugins\Example;

use App\Contracts\Plugin;

class ExamplePlugin implements Plugin
{
    public function register()
    {
        // Register plugin services
    }

    public function boot()
    {
        // Boot plugin
    }

    public function provides()
    {
        return [
            'example.service'
        ];
    }

    public function handle($request, $next)
    {
        // Plugin middleware logic
        return $next($request);
    }
} 