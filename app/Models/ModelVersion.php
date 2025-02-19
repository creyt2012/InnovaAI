<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelVersion extends Model
{
    protected $fillable = [
        'model_id',
        'version',
        'description',
        'parameters',
        'performance_metrics',
        'is_active',
        'trained_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'performance_metrics' => 'array',
        'is_active' => 'boolean',
        'trained_at' => 'datetime'
    ];
} 