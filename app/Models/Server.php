<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Server extends Model
{
    protected $fillable = [
        'name',
        'host',
        'port',
        'status',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'json',
        'last_ping_at' => 'datetime',
    ];

    public function metrics(): HasMany
    {
        return $this->hasMany(ServerMetric::class);
    }

    public function latestMetrics(): HasOne
    {
        return $this->hasOne(ServerMetric::class)->latestOfMany();
    }

    public function isHealthy(): bool
    {
        return $this->status === 'active' && 
               $this->last_ping_at && 
               $this->last_ping_at->diffInMinutes() < 5;
    }
} 