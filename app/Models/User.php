<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'model_parameters' => 'array'
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'preferred_model_id',
        'model_parameters'
    ];

    public function preferredModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class, 'preferred_model_id');
    }
} 