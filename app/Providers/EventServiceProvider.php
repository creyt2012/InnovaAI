<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ChatMessageSent;
use App\Events\AIResponseGenerated;
use App\Events\ConversationCreated;
use App\Listeners\ProcessAIResponse;
use App\Listeners\NotifyUser;
use App\Listeners\SaveAIResponse;
use App\Listeners\InitializeAIContext;
use App\Listeners\LogUserActivity;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            LogUserActivity::class,
        ],
        Logout::class => [
            LogUserActivity::class,
        ],
        ChatMessageSent::class => [
            ProcessAIResponse::class,
            NotifyUser::class,
        ],
        AIResponseGenerated::class => [
            SaveAIResponse::class,
            NotifyUser::class,
        ],
        ConversationCreated::class => [
            InitializeAIContext::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 