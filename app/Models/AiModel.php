<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class AiModel extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'path',
        'description',
        'category',
        'parameters',
        'context_length',
        'is_active'
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean'
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function getStatus(): string 
    {
        if (!$this->is_active) {
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