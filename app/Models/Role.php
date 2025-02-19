<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_system' // Để đánh dấu role mặc định không được xóa
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean'
    ];
} 