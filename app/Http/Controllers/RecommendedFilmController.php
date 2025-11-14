<?php

namespace App\Http\Controllers;

use App\Models\RecommendedFilm;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendedFilmController extends Controller
{
    private TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index()
    {
        $today = now()->toDateString();
        $lastUpdate = Cache::get('films_last_update');
        $needsRefresh = (!$lastUpdate || $lastUpdate !== $today || RecommendedFilm::count() === 0);

        if ($needsRefresh && Cache::add('films_loading', true, 600)) {
            try {
                $this->loadInitialFilms();
                Cache::put('films_last_update', $today, 86400);
            } catch (\Throwable $e) {
                Log::error('Errore aggiornamento film: ' . $e->getMessage());
            } finally {
                Cache::forget('films_loading');
            }
        }

        $films = RecommendedFilm::orderBy('imdb_rating', 'desc')->paginate(24);
        return view('recommended-films.index', compact('films'));
    }

    public function show($id)
    {
        $film = RecommendedFilm::with(['comments.user'])->findOrFail($id);

        $userFilmList = null;
        $isFavoriteFilm = false;

        if (auth()->check()) {
            $user = auth()->user();

            $userFilmList = $user->filmLists()
                ->where('recommended_film_id', $id)
                ->first();

            $isFavoriteFilm = $user->favoriteFilms()->where('recommended_film_id', $id)->exists();
        }

        return view('recommended-films.show', compact('film', 'userFilmList', 'isFavoriteFilm'));
    }

    private function loadInitialFilms(): void
    {
        $maxPages = 5;

        for ($page = 1; $page <= $maxPages; $page++) {
            $data = $this->tmdbService->getTopRatedMovies($page);
            $results = $data['results'] ?? [];

            foreach ($results as $movie) {
                $tmdbId = $movie['id'] ?? null;
                if (!$tmdbId) continue;

                $year = null;
                if (!empty($movie['release_date'])) {
                    $year = (int) date('Y', strtotime($movie['release_date']));
                }

                RecommendedFilm::updateOrCreate(
                    ['imdb_id' => 'tmdb_' . $tmdbId],
                    [
                        'title'       => $movie['title'] ?? 'Titolo non disponibile',
                        'year'        => $year,
                        'plot'        => $movie['overview'] ?? 'Descrizione non disponibile',
                        'director'    => null,
                        'actors'      => null,
                        'genre'       => null,
                        'poster_url'  => $this->tmdbService->getImageUrl($movie['poster_path'] ?? null),
                        'imdb_rating' => isset($movie['vote_average']) ? round($movie['vote_average'], 1) : null,
                        'runtime'     => null,
                        'rated'       => !empty($movie['adult']) ? 'VM18' : 'T',
                        'released'    => $movie['release_date'] ?? null,
                    ]
                );
            }
            usleep(150000);
        }
    }

}