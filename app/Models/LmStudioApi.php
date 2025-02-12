<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmStudioApi extends Model
{
    protected $fillable = [
        'name',
        'endpoint',
        'api_key',
        'model',
        'max_tokens',
        'temperature',
        'status',
        'priority',
        'rate_limit',
        'timeout',
        'last_check',
    ];

    protected $casts = [
        'last_check' => 'datetime',
        'max_tokens' => 'integer',
        'temperature' => 'float',
        'priority' => 'integer',
        'rate_limit' => 'integer',
        'timeout' => 'integer',
    ];

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrioritized($query)
    {
        return $query->orderBy('priority', 'desc');
    }
} 