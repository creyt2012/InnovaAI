<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'hero_title',
        'hero_description',
        'hero_image',
        'features',
        'testimonials',
        'pricing_title',
        'pricing_description',
        'contact_email',
        'social_links',
        'footer_text',
        'meta_title',
        'meta_description',
        'custom_css',
        'custom_js'
    ];

    protected $casts = [
        'features' => 'array',
        'testimonials' => 'array',
        'social_links' => 'array'
    ];
} 