<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_message',
        'ai_response',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 