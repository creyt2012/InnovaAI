<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\LMStudioService;

class LMStudioServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(LMStudioService::class, function ($app) {
            return new LMStudioService();
        });
    }

    public function boot()
    {
        //
    }
} 