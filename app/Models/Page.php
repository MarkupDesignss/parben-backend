<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'desc_image',
        'partner_images',
        'partner_cards',
        'is_active'
    ];

    protected $casts = [
        'partner_images' => 'array',
        'partner_cards' => 'array',
        'is_active' => 'boolean'
    ];
}
