<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class KnowledgeBase extends Model
{
    use Searchable;

    protected $fillable = [
        'title',
        'content',
        'source_type',
        'source_url',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_trained_at' => 'datetime'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'tags' => $this->tags
        ];
    }

    public function train()
    {
        // Implement training logic
    }
} 