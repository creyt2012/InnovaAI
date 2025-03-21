<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AIService;

class AIServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService();
        });
    }

    public function boot()
    {
        //
    }
} 