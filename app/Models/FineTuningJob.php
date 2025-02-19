<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineTuningJob extends Model
{
    protected $fillable = [
        'model_id',
        'status',
        'training_config',
        'metrics'
    ];

    protected $casts = [
        'training_config' => 'array',
        'metrics' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];
} 