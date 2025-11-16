<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteFilms()
    {
        return $this->belongsToMany(RecommendedFilm::class, 'favorites', 'user_id', 'recommended_film_id')
            ->withTimestamps();
    }

    public function filmLists()
    {
        return $this->hasMany(UserFilmList::class);
    }

    public function planToWatch()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_PLAN_TO_WATCH);
    }

    public function watching()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_WATCHING);
    }

    public function completed()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_COMPLETED);
    }

    public function dropped()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_DROPPED);
    }
}