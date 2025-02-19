<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\PromptTemplatePolicy;
use App\Models\PromptTemplate;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::policy(PromptTemplate::class, PromptTemplatePolicy::class);
    }
} 