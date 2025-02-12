<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomEvent extends Model
{
    protected $fillable = [
        'name',
        'data',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 