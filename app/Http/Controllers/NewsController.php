<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    private $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index()
    {
        $this->syncNewsFromApi();

        $news = News::orderBy('published_at', 'desc')->paginate(12);

        return view('news.index', compact('news'));
    }

    public function show($id)
    {
        $news = News::with(['comments.user'])->find($id);
        
        if (!$news) {
            return redirect()->route('news.index')->with('error', 'Notizia non trovata!');
        }
        
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = auth()->user()->favoriteNews()->where('news_id', $id)->exists();
        }

        return view('news.show', compact('news', 'isFavorite'));
    }

    private function syncNewsFromApi()
    {
        try {
            $moviesData = $this->tmdbService->getPopularMovies(1);
            
            if ($moviesData && isset($moviesData['results'])) {
                $movies = $moviesData['results'];
                
                Log::info('Film TMDb recuperati: ' . count($movies));

                foreach ($movies as $movie) {
                    News::updateOrCreate(
                        ['external_id' => 'tmdb_movie_' . $movie['id']],
                        [
                            'title' => $movie['title'] ?? 'Titolo non disponibile',
                            'description' => $movie['overview'] ?? 'Descrizione non disponibile',
                            'content' => $movie['overview'] ?? 'Contenuto non disponibile',
                            'image_url' => $this->tmdbService->getImageUrl($movie['poster_path'] ?? null),
                            'source' => 'TMDb - Film',
                            'published_at' => isset($movie['release_date']) && !empty($movie['release_date'])
                                ? $movie['release_date'] 
                                : now(),
                        ]
                    );
                }
            }

            $tvData = $this->tmdbService->getPopularTVShows(1);
            
            if ($tvData && isset($tvData['results'])) {
                $shows = $tvData['results'];
                
                Log::info('Serie TV TMDb recuperate: ' . count($shows));

                foreach ($shows as $show) {
                    News::updateOrCreate(
                        ['external_id' => 'tmdb_tv_' . $show['id']],
                        [
                            'title' => $show['name'] ?? 'Titolo non disponibile',
                            'description' => $show['overview'] ?? 'Descrizione non disponibile',
                            'content' => $show['overview'] ?? 'Contenuto non disponibile',
                            'image_url' => $this->tmdbService->getImageUrl($show['poster_path'] ?? null),
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