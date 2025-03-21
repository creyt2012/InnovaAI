<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return $user->conversations->contains($conversationId);
});

Broadcast::channel('ai.processing.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
}); 