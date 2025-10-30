<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class News extends Model
{
    protected $table = 'news';
    
    protected $fillable = [
        'external_id',
        'title',
        'description',
        'content',
        'image_url',
        'source',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}