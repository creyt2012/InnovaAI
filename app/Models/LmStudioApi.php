<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmStudioApi extends Model
{
    protected $fillable = [
        'name',
        'endpoint',
        'api_key',
        'configuration',
        'is_active'
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean'
    ];
} 