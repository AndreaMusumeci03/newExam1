<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFilmList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommended_film_id',
        'status',
        'rating',
        'personal_notes',
    ];

    public const STATUS_PLAN_TO_WATCH = 'plan_to_watch';
    public const STATUS_WATCHING = 'watching';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DROPPED = 'dropped';

    public static function getStatusLabels()
    {
        return [
            self::STATUS_PLAN_TO_WATCH => '📋 Da Vedere',
            self::STATUS_WATCHING => '▶️ Sto Guardando',
            self::STATUS_COMPLETED => '✅ Completato',
            self::STATUS_DROPPED => '❌ Abbandonato',
        ];
    }

    public function getStatusEmoji()
    {
        $emojis = [
            self::STATUS_PLAN_TO_WATCH => '📋',
            self::STATUS_WATCHING => '▶️',
            self::STATUS_COMPLETED => '✅',
            self::STATUS_DROPPED => '❌',
        ];
        return $emojis[$this->status] ?? '📋';
    }

    public function getStatusLabel()
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? 'Sconosciuto';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recommendedFilm()
    {
        return $this->belongsTo(RecommendedFilm::class);
    }

    public function getTitle()
    {
        return $this->recommendedFilm ? $this->recommendedFilm->title : 'Sconosciuto';
    }

    public function getImageUrl()
    {
        return $this->recommendedFilm ? $this->recommendedFilm->poster_url : null;
    }
}