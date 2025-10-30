<?php

namespace App\Http\Controllers;

use App\Models\RecommendedFilm;
use App\Services\OmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecommendedFilmController extends Controller
{
    private $omdbService;

    public function __construct()
    {
        $this->omdbService = new OmdbService();
    }

    // Mostra tutti i film consigliati
    public function index()
    {
        // Carica i film solo se il database è vuoto
        if (RecommendedFilm::count() === 0) {
            $this->loadFilmsFromOmdb();
        }

        // Recupera tutti i film dal database con paginazione
        $films = RecommendedFilm::orderBy('imdb_rating', 'desc')->paginate(24);

        return view('recommended-films.index', compact('films'));
    }

    // ✅ Mostra un singolo film (SENZA TRADUZIONE)
    public function show($id)
    {
        $film = RecommendedFilm::findOrFail($id);
        
        // Controlla se l'utente lo ha già nella sua lista
        $userFilmList = null;
        if (auth()->check()) {
            $userFilmList = auth()->user()->filmLists()
                ->where('recommended_film_id', $id)
                ->first();
        }

        return view('recommended-films.show', compact('film', 'userFilmList'));
    }

    // Carica i film da OMDb (solo al primo accesso)
    private function loadFilmsFromOmdb()
    {
        try {
            Log::info('Caricamento film da OMDb...');
            
            $movies = $this->omdbService->getPopularMovies();
            $savedCount = 0;

            foreach ($movies as $movie) {
                $details = $this->omdbService->getByImdbId($movie['imdbID']);

                if ($details) {
                    RecommendedFilm::updateOrCreate(
                        ['imdb_id' => $movie['imdbID']],
                        [
                            'title' => $details['Title'] ?? 'Titolo non disponibile',
                            'year' => isset($details['Year']) && is_numeric($details['Year']) ? (int)$details['Year'] : null,
                            'plot' => $details['Plot'] ?? null,
                            'director' => $details['Director'] ?? null,
                            'actors' => $details['Actors'] ?? null,
                            'genre' => $details['Genre'] ?? null,
                            'poster_url' => ($details['Poster'] !== 'N/A') ? $details['Poster'] : null,
                            'imdb_rating' => isset($details['imdbRating']) && $details['imdbRating'] !== 'N/A' ? (float)$details['imdbRating'] : null,
                            'runtime' => $details['Runtime'] ?? null,
                            'rated' => $details['Rated'] ?? null,
                            'released' => isset($details['Released']) && $details['Released'] !== 'N/A'
                                ? date('Y-m-d', strtotime($details['Released']))
                                : null,
                        ]
                    );
                    $savedCount++;
                }

                usleep(250000); // 0.25 secondi
            }

            Log::info("Film caricati: {$savedCount}");

        } catch (\Exception $e) {
            Log::error('Errore caricamento film: ' . $e->getMessage());
        }
    }
}