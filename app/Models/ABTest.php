<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ABTest extends Model
{
    protected $fillable = [
        'name',
        'description',
        'control_variant',
        'test_variant',
        'metrics',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'metrics' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
} 