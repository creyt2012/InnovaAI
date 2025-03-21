<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class AiModel extends Model
{
    protected $fillable = [
        'name',
        'endpoint',
        'api_key',
        'type', // openai, lmstudio, etc
        'status', // active, inactive
        'priority',
        'max_tokens',
        'temperature',
        'context_length'
    ];

    protected $casts = [
        'status' => 'boolean',
        'priority' => 'integer',
        'max_tokens' => 'integer',
        'temperature' => 'float',
        'context_length' => 'integer'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'preferred_model_id');
    }

    public function isAvailable()
    {
        return $this->status === 'active';
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function getStatus(): string 
    {
        if (!$this->status) {
            return 'offline';
        }

        try {
            // Kiểm tra kết nối tới model trên LMStudio server
            $response = Http::timeout(2)->get($this->server->url . '/v1/models');
            $models = $response->json()['data'] ?? [];
            
            // Kiểm tra xem model path có tồn tại trong danh sách
            $modelExists = collect($models)->contains('path', $this->path);
            
            return $modelExists ? 'online' : 'offline';
        } catch (\Exception $e) {
            return 'offline';
        }
    }

    public function getLatency(): int
    {
        if ($this->getStatus() === 'offline') {
            return 0;
        }

        try {
            $start = microtime(true);
            Http::get($this->server->url . '/v1/models');
            $end = microtime(true);
            
            return (int) (($end - $start) * 1000); // Convert to milliseconds
        } catch (\Exception $e) {
            return 0;
        }
    }
} 