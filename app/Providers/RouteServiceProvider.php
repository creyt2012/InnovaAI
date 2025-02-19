<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            // ...
            Route::middleware('web')
                 ->prefix('admin')
                 ->name('admin.')
                 ->group(base_path('routes/admin.php'));
        });
    }
} 