<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LMStudioNode extends Model
{
    protected $fillable = [
        'name',
        'url',
        'is_active',
        'priority',
        'max_tokens',
        'temperature',
        'timeout'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'max_tokens' => 'integer',
        'temperature' => 'float',
        'timeout' => 'integer'
    ];
} 