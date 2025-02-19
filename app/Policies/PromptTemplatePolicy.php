<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PromptTemplate;

class PromptTemplatePolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, PromptTemplate $template)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, PromptTemplate $template)
    {
        return $user->isAdmin();
    }
} 