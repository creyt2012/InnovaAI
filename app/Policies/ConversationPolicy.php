<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConversationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Conversation $conversation)
    {
        return $user->id === $conversation->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Conversation $conversation)
    {
        return $user->id === $conversation->user_id;
    }

    public function delete(User $user, Conversation $conversation)
    {
        return $user->id === $conversation->user_id;
    }

    public function restore(User $user, Conversation $conversation)
    {
        return $user->id === $conversation->user_id;
    }

    public function forceDelete(User $user, Conversation $conversation)
    {
        return $user->isAdmin();
    }
} 