<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'website',
        'github',
        'bio',
        'status', // verified, pending, banned
        'verification_token',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plugins()
    {
        return $this->hasMany(Plugin::class);
    }
} 