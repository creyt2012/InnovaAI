<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'font_size',
        'notification_enabled',
        'keyboard_shortcuts',
        'display_mode'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 