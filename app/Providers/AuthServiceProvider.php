<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Conversation;
use App\Policies\ConversationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Conversation::class => ConversationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }

        // Configure Passport tokens
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::personalAccessTokensExpireIn(Carbon::now()->addMonths(6));

        // Define gates
        Gate::define('access-admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-ai-models', function (User $user) {
            return $user->isAdmin();
        });
    }
} 