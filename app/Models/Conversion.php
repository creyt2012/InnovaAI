<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $fillable = [
        'goal_id',
        'visitor_id',
        'value',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'value' => 'float'
    ];

    public function goal()
    {
        return $this->belongsTo(ConversionGoal::class);
    }

    public function visitor()
    {
        return $this->belongsTo(VisitorAnalytic::class);
    }
} 