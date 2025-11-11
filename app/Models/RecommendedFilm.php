<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendedFilm extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id',        
        'title',
        'year',
        'plot',
        'director',
        'actors',
        'genre',
        'poster_url',
        'imdb_rating',
        'runtime',
        'rated',
        'released',
    ];

    protected $casts = [
        'released' => 'date',
        'imdb_rating' => 'decimal:1',
    ];

    public function userLists()
    {
        return $this->hasMany(UserFilmList::class, 'recommended_film_id');
    }
}