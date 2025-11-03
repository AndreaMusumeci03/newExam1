<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    private $apiKey;
    private $baseUrl = 'https://api.themoviedb.org/3';
    private $imageBaseUrl = 'https://image.tmdb.org/t/p/';
    private $language = 'it-IT';

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
    }

 
    private function makeRequest($url, $params = [])
    {
       
        return Http::withoutVerifying()
            ->timeout(30)
            ->get($url, $params);
    }

   
    public function getPopularMovies($page = 1)
    {
        if (empty($this->apiKey)) {
            Log::warning('TMDB_API_KEY non configurato');
            return null;
        }

        try {
            return Cache::remember("tmdb_popular_movies_it_page_{$page}", 3600, function () use ($page) {
                $response = $this->makeRequest("{$this->baseUrl}/movie/popular", [
                    'api_key' => $this->apiKey,
                    'page' => $page,
                    'language' => $this->language,
                    'region' => 'IT',
                ]);

                return $response->successful() ? $response->json() : null;
            });
        } catch (\Exception $e) {
            Log::error('TMDb getPopularMovies error: ' . $e->getMessage());
            return null;
        }
    }

  
    public function getPopularTVShows($page = 1)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            return Cache::remember("tmdb_popular_tv_it_page_{$page}", 3600, function () use ($page) {
                $response = $this->makeRequest("{$this->baseUrl}/tv/popular", [
                    'api_key' => $this->apiKey,
                    'page' => $page,
                    'language' => $this->language,
                    'region' => 'IT',
                ]);

                return $response->successful() ? $response->json() : null;
            });
        } catch (\Exception $e) {
            Log::error('TMDb getPopularTVShows error: ' . $e->getMessage());
            return null;
        }
    }

  
    public function getTrendingMovies($timeWindow = 'week', $page = 1)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            return Cache::remember("tmdb_trending_it_{$timeWindow}_page_{$page}", 3600, function () use ($timeWindow, $page) {
                $response = $this->makeRequest("{$this->baseUrl}/trending/movie/{$timeWindow}", [
                    'api_key' => $this->apiKey,
                    'page' => $page,
                    'language' => $this->language,
                ]);

                return $response->successful() ? $response->json() : null;
            });
        } catch (\Exception $e) {
            Log::error('TMDb getTrendingMovies error: ' . $e->getMessage());
            return null;
        }
    }

   
    public function getTopRatedMovies($page = 1)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            return Cache::remember("tmdb_top_rated_it_page_{$page}", 3600, function () use ($page) {
                $response = $this->makeRequest("{$this->baseUrl}/movie/top_rated", [
                    'api_key' => $this->apiKey,
                    'page' => $page,
                    'language' => $this->language,
                    'region' => 'IT',
                ]);

                return $response->successful() ? $response->json() : null;
            });
        } catch (\Exception $e) {
            Log::error('TMDb getTopRatedMovies error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Dettagli completi di un film
     */
    public function getMovieDetails($movieId)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            return Cache::remember("tmdb_movie_it_{$movieId}", 3600, function () use ($movieId) {
                $response = $this->makeRequest("{$this->baseUrl}/movie/{$movieId}", [
                    'api_key' => $this->apiKey,
                    'language' => $this->language,
                    'append_to_response' => 'credits,videos,similar,keywords',
                    'include_image_language' => 'it,null',
                ]);

                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json();

                // Fallback in inglese se la trama è vuota
                if (empty($data['overview'])) {
                    $responseEn = $this->makeRequest("{$this->baseUrl}/movie/{$movieId}", [
                        'api_key' => $this->apiKey,
                        'language' => 'en-US',
                    ]);

                    if ($responseEn->successful()) {
                        $dataEn = $responseEn->json();
                        $data['overview'] = $dataEn['overview'] ?? 'Descrizione non disponibile';
                    }
                }

                return $data;
            });
        } catch (\Exception $e) {
            Log::error('TMDb getMovieDetails error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cerca film per titolo
     */
    public function searchMovies($query, $page = 1)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = $this->makeRequest("{$this->baseUrl}/search/movie", [
                'api_key' => $this->apiKey,
                'query' => $query,
                'page' => $page,
                'language' => $this->language,
                'region' => 'IT',
                'include_adult' => false,
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('TMDb searchMovies error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Film per genere
     */
    public function getMoviesByGenre($genreId, $page = 1)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = $this->makeRequest("{$this->baseUrl}/discover/movie", [
                'api_key' => $this->apiKey,
                'with_genres' => $genreId,
                'page' => $page,
                'language' => $this->language,
                'region' => 'IT',
                'sort_by' => 'popularity.desc',
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('TMDb getMoviesByGenre error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista generi
     */
    public function getGenres()
    {
        if (empty($this->apiKey)) {
            return [];
        }

        try {
            return Cache::remember('tmdb_genres_it', 86400, function () {
                $response = $this->makeRequest("{$this->baseUrl}/genre/movie/list", [
                    'api_key' => $this->apiKey,
                    'language' => $this->language,
                ]);

                return $response->successful() ? $response->json()['genres'] : [];
            });
        } catch (\Exception $e) {
            Log::error('TMDb getGenres error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Film raccomandati basati su un film
     */
    public function getRecommendations($movieId, $page = 1)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = $this->makeRequest("{$this->baseUrl}/movie/{$movieId}/recommendations", [
                'api_key' => $this->apiKey,
                'page' => $page,
                'language' => $this->language,
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('TMDb getRecommendations error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * URL immagine ottimizzato
     */
    public function getImageUrl($path, $size = 'w500')
    {
        if (empty($path)) {
            return null;
        }

        return "{$this->imageBaseUrl}{$size}{$path}";
    }

    /**
     * URL backdrop (immagine di sfondo)
     */
    public function getBackdropUrl($path, $size = 'w1280')
    {
        if (empty($path)) {
            return null;
        }

        return "{$this->imageBaseUrl}{$size}{$path}";
    }
}