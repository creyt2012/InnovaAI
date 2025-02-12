<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionGoal extends Model
{
    protected $fillable = [
        'name',
        'type', // pageview, event, custom
        'target', // URL or event name
        'conditions', // JSON conditions
        'value',
        'is_active'
    ];

    protected $casts = [
        'conditions' => 'array',
        'value' => 'float',
        'is_active' => 'boolean'
    ];

    public function conversions()
    {
        return $this->hasMany(Conversion::class);
    }
} 