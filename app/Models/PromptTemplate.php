<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptTemplate extends Model
{
    protected $fillable = [
        'name',
        'content',
        'category',
        'parameters',
        'is_active'
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean'
    ];

    public function compile(array $params = [])
    {
        $content = $this->content;
        foreach ($params as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        return $content;
    }
} 