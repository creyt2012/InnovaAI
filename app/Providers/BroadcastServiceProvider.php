<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['auth:api']]);

        require base_path('routes/channels.php');

        Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
            return (int) $user->id === (int) $id;
        });

        // Add AI specific channels
        Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
            return $user->conversations->contains($conversationId);
        });

        Broadcast::channel('ai.processing.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });
    }
} 