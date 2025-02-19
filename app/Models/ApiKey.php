<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'key',
        'permissions',
        'rate_limit',
        'expires_at',
        'last_used_at',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean'
    ];
} 