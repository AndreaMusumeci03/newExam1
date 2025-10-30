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

    // Relazioni
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteNews()
    {
        return $this->belongsToMany(News::class, 'favorites')->withTimestamps();
    }

    // ✅ NUOVE RELAZIONI PER LE LISTE
    public function filmLists()
    {
        return $this->hasMany(UserFilmList::class);
    }

    // Film da vedere
    public function planToWatch()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_PLAN_TO_WATCH);
    }

    // Film in visione
    public function watching()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_WATCHING);
    }

    // Film completati
    public function completed()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_COMPLETED);
    }

    // Film abbandonati
    public function dropped()
    {
        return $this->filmLists()->where('status', UserFilmList::STATUS_DROPPED);
    }
}