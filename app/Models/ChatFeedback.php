<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatFeedback extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'rating',
        'comment',
        'improvement_suggestions',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array'
    ];
} 