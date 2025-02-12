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
        'category_id',
        'tags',
        'status',
        'author_id'
    ];

    protected $casts = [
        'tags' => 'array'
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
} 