<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        if(config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Monitor failed jobs
        Queue::failing(function (JobFailed $event) {
            Log::error('Job Failed', [
                'job' => get_class($event->job),
                'exception' => $event->exception->getMessage(),
                'trace' => $event->exception->getTraceAsString()
            ]);
        });

        // Rate Limiting Configuration
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting
     */
    protected function configureRateLimiting(): void
    {
        // Rate limiting configuration can be moved here from RouteServiceProvider if needed
    }
} 