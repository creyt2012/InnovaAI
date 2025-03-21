<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        parent::boot();

        $this->configureRateLimiting();

        $this->routes(function () {
            // API Routes
            Route::middleware(['api', 'api_version:v1'])
                ->prefix('api/v1')
                ->group(base_path('routes/api_v1.php'));

            // Web Routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Admin Routes
            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            // AI Chat Routes
            Route::middleware(['api', 'auth:api'])
                ->prefix('api/ai')
                ->group(base_path('routes/ai.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // API rate limiting
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // AI chat rate limiting
        RateLimiter::for('ai_chat', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(20)->by($request->user()->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        // Login rate limiting
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Registration rate limiting
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
} 