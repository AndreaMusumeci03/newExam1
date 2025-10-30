<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    // Mostra tutte le news
    public function index()
    {
        // Sincronizza le news dall'API esterna
        $this->syncNewsFromApi();

        // Recupera tutte le news dal database
        $news = News::orderBy('published_at', 'desc')->paginate(12);

        return view('news.index', compact('news'));
    }

    // Mostra una singola news
    public function show($id)
    {
        $news = News::with(['comments.user'])->find($id);
        
        if (!$news) {
            return redirect()->route('news.index')->with('error', 'Notizia non trovata!');
        }
        
        // Controlla se l'utente ha già aggiunto ai preferiti
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = auth()->user()->favoriteNews()->where('news_id', $id)->exists();
        }

        return view('news.show', compact('news', 'isFavorite'));
    }

    // Sincronizza le news da TMDb API (SOLO TMDB)
    private function syncNewsFromApi()
    {
        $apiKey = env('TMDB_API_KEY', '');
        
        if (empty($apiKey)) {
            Log::warning('TMDB_API_KEY non configurato nel file .env');
            return;
        }

        try {
            // Recupera film popolari
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get('https://api.themoviedb.org/3/movie/popular', [
                    'api_key' => $apiKey,
                    'language' => 'it-IT',
                    'page' => 1,
                ]);

            if ($response->successful()) {
                $movies = $response->json()['results'] ?? [];
                
                Log::info('Film TMDb recuperati: ' . count($movies));

                foreach ($movies as $movie) {
                    News::updateOrCreate(
                        ['external_id' => 'tmdb_movie_' . $movie['id']],
                        [
                            'title' => $movie['title'] ?? 'Titolo non disponibile',
                            'description' => $movie['overview'] ?? 'Descrizione non disponibile',
                            'content' => $movie['overview'] ?? 'Contenuto non disponibile',
                            'image_url' => isset($movie['poster_path']) 
                                ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
                                : null,
                            'source' => 'TMDb - Film',
                            'published_at' => isset($movie['release_date']) && !empty($movie['release_date'])
                                ? $movie['release_date'] 
                                : now(),
                        ]
                    );
                }
            }

            // Recupera serie TV popolari
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get('https://api.themoviedb.org/3/tv/popular', [
                    'api_key' => $apiKey,
                    'language' => 'it-IT',
                    'page' => 1,
                ]);

            if ($response->successful()) {
                $shows = $response->json()['results'] ?? [];
                
                Log::info('Serie TV TMDb recuperate: ' . count($shows));

                foreach ($shows as $show) {
                    News::updateOrCreate(
                        ['external_id' => 'tmdb_tv_' . $show['id']],
                        [
                            'title' => $show['name'] ?? 'Titolo non disponibile',
                            'description' => $show['overview'] ?? 'Descrizione non disponibile',
                            'content' => $show['overview'] ?? 'Contenuto non disponibile',
                            'image_url' => isset($show['poster_path']) 
                                ? 'https://image.tmdb.org/t/p/w500' . $show['poster_path']
                                : null,
                            'source' => 'TMDb - Serie TV',
                            'published_at' => isset($show['first_air_date']) && !empty($show['first_air_date'])
                                ? $show['first_air_date'] 
                                : now(),
                        ]
                    );
                }
            }

        } catch (\Exception $e) {
            Log::error('Errore sincronizzazione TMDb: ' . $e->getMessage());
        }
    }
}