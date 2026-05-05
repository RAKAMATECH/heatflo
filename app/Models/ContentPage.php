<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pathname',
        'slug',
        'title',
        'excerpt',
        'body_html',
        'blocks',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
