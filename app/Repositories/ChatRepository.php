<?php

namespace App\Repositories;

use App\Models\Chat;

class ChatRepository
{
    public function getLatestChats($userId, $limit = 10)
    {
        return Chat::where('user_id', $userId)
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }

    public function create(array $data)
    {
        return Chat::create($data);
    }
} 