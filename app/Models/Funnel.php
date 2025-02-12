<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funnel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'steps', // JSON array of steps
        'is_active'
    ];

    protected $casts = [
        'steps' => 'array',
        'is_active' => 'boolean'
    ];

    public function getConversionRate()
    {
        // Calculate funnel conversion rate
    }
} 