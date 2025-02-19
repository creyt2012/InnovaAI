<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAlert extends Model
{
    protected $fillable = [
        'type',
        'message',
        'severity',
        'status',
        'resolved_at',
        'resolved_by'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];
} 