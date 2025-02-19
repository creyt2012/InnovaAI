<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'initial_message',
        'context',
        'parameters',
        'is_active'
    ];

    protected $casts = [
        'parameters' => 'array',
        'context' => 'array',
        'is_active' => 'boolean'
    ];
} 