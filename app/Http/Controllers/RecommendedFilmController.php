<?php

namespace App\Http\Controllers;

use App\Models\RecommendedFilm;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendedFilmController extends Controller
{
    private $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    // Mostra tutti i film consigliati
    public function index()
    {
        // Verifica se i film devono essere aggiornati (una volta al giorno)
        $lastUpdate = Cache::get('films_last_update');
        $today = now()->format('Y-m-d');
        $isLoading = Cache::get('films_loading', false);

        // Se non ci sono film E non sta già caricando, avvia il caricamento
        if ((!$lastUpdate || $lastUpdate !== $today || RecommendedFilm::count() === 0) && !$isLoading) {
            // Imposta il flag di caricamento per evitare caricamenti multipli
            Cache::put('films_loading', true, 600); // 10 minuti
            
            // Carica solo pochi film inizialmente (per mostrare subito qualcosa)
            $this->loadInitialFilms();
            
            // Salva la data dell'ultimo aggiornamento
            Cache::put('films_last_update', $today, 86400);
            Cache::forget('films_loading');
        }

        // Recupera i film dal database (anche se sono pochi)
        $films = RecommendedFilm::orderBy('imdb_rating', 'desc')->paginate(24);

        return view('recommended-films.index', compact('films'));
    }

    // Mostra un singolo film
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

    // ✅ NUOVO: Carica solo i film iniziali (veloce)
    private function loadInitialFilms()
    {
        try {
            Log::info('Caricamento film iniziali da TMDb...');
            
            // Svuota i vecchi film
            $this->clearOldFilms();
            
            $savedCount = 0;
            $maxPages = 5; // Solo 5 pagine = 100 film (molto più veloce!)
            
            for ($page = 1; $page <= $maxPages; $page++) {
                $data = $this->tmdbService->getTopRatedMovies($page);
                
                if (!$data || !isset($data['results'])) {
                    continue;
                }

                foreach ($data['results'] as $movie) {
                    // Salva solo i dati base senza chiamare getMovieDetails
                    // (molto più veloce!)
                    
                    $year = null;
                    if (!empty($movie['release_date'])) {
                        $year = (int) date('Y', strtotime($movie['release_date']));
                    }

                    RecommendedFilm::create([
                        'imdb_id' => 'tmdb_' . $movie['id'],
                        'title' => $movie['title'] ?? 'Titolo non disponibile',
                        'year' => $year,
                        'plot' => $movie['overview'] ?? 'Descrizione non disponibile',
                        'director' => null, // Verrà aggiunto dopo
                        'actors' => null, // Verrà aggiunto dopo
                        'genre' => null, // Verrà aggiunto dopo
                        'poster_url' => $this->tmdbService->getImageUrl($movie['poster_path'] ?? null),
                        'imdb_rating' => isset($movie['vote_average']) ? round($movie['vote_average'], 1) : null,
                        'runtime' => null,
                        'rated' => $movie['adult'] ? 'VM18' : 'T',
                        'released' => $movie['release_date'] ?? null,
                    ]);
                    
                    $savedCount++;
                }

                // Piccola pausa
                usleep(250000); // 0.25 secondi
            }

            Log::info("Film iniziali caricati: {$savedCount}");

        } catch (\Exception $e) {
            Log::error('Errore caricamento film iniziali: ' . $e->getMessage());
        }
    }

    // Svuota i vecchi film
    private function clearOldFilms()
    {
        try {
            // Prima elimina le referenze nelle liste utenti
            DB::table('user_film_lists')
                ->whereNotNull('recommended_film_id')
                ->delete();
            
            // Poi elimina i film
            RecommendedFilm::query()->delete();
            
            // Resetta l'auto-increment
            DB::statement('ALTER TABLE recommended_films AUTO_INCREMENT = 1');
            
            Log::info('Vecchi film rimossi con successo');
        } catch (\Exception $e) {
            Log::error('Errore rimozione vecchi film: ' . $e->getMessage());
        }
    }

    // ✅ NUOVO: Metodo per caricare i dettagli completi in background
    // Questo può essere chiamato da un comando Artisan o una route separata
    public function loadFullDetails()
    {
        try {
            Log::info('Caricamento dettagli completi film...');
            
            $films = RecommendedFilm::whereNull('director')->get();
            $updatedCount = 0;
            
            foreach ($films as $film) {
                // Estrai l'ID TMDb dall'imdb_id
                $tmdbId = str_replace('tmdb_', '', $film->imdb_id);
                
                // Ottieni i dettagli completi
                $details = $this->tmdbService->getMovieDetails($tmdbId);
                
                if (!$details) {
                    continue;
                }

                // Estrai il regista
                $director = null;
                if (isset($details['credits']['crew'])) {
                    foreach ($details['credits']['crew'] as $crew) {
                        if ($crew['job'] === 'Director') {
                            $director = $crew['name'];
                            break;
                        }
                    }
                }

                // Estrai gli attori
                $actors = null;
                if (isset($details['credits']['cast'])) {
                    $cast = array_slice($details['credits']['cast'], 0, 5);
                    $actors = implode(', ', array_column($cast, 'name'));
                }

                // Estrai i generi
                $genres = null;
                if (isset($details['genres']) && !empty($details['genres'])) {
                    $genres = implode(', ', array_column($details['genres'], 'name'));
                }

                // Aggiorna il film
                $film->update([
                    'director' => $director,
                    'actors' => $actors,
                    'genre' => $genres,
                    'runtime' => isset($details['runtime']) ? $details['runtime'] . ' min' : null,
                ]);
                
                $updatedCount++;
                
                usleep(250000); // 0.25 secondi
            }

            Log::info("Dettagli completi aggiunti a {$updatedCount} film");
            
            return response()->json([
                'success' => true,
                'message' => "Dettagli aggiunti a {$updatedCount} film"
            ]);

        } catch (\Exception $e) {
            Log::error('Errore caricamento dettagli: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}