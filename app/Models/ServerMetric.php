<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerMetric extends Model
{
    protected $fillable = [
        'server_id',
        'cpu_usage',
        'memory_usage',
        'disk_usage',
        'active_connections',
        'requests_per_minute',
        'response_time',
        'additional_metrics',
    ];

    protected $casts = [
        'additional_metrics' => 'json',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
} 