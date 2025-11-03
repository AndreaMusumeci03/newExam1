<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    protected string $base = 'https://api.themoviedb.org/3';
    protected string $token;
    protected string $lang;

    public function __construct()
    {
        $this->token = (string) config('services.tmdb.token');      
        $this->lang  = (string) config('services.tmdb.lang', 'it-IT');
    }

    protected function request()
    {
        return Http::withOptions(['verify' => false])->acceptJson();
    }

    protected function get(string $path, array $params = []): array
    {
        $query = array_merge([
            'api_key'  => $this->token,
            'language' => $this->lang,
        ], $params);

        $response = $this->request()
            ->get($this->base . $path, $query)
            ->throw();

        return $response->json() ?? [];
    }

    public function getTopRatedMovies(int $page = 1): array
    {
        return $this->get('/movie/top_rated', ['page' => $page]);
    }

    public function getMovieDetails(int|string $id): array
    {
        return $this->get("/movie/{$id}", [
            'append_to_response' => 'credits,videos,images',
        ]);
    }

    public function getPopularMovies(int $page = 1): array
    {
        return $this->get('/movie/popular', ['page' => $page]);
    }

    public function getPopularTVShows(int $page = 1): array
    {
        return $this->get('/tv/popular', ['page' => $page]);
    }

    public function getImageUrl(?string $path, string $size = 'w500'): ?string
    {
        if (!$path) {
            return null;
        }
        return "https://image.tmdb.org/t/p/{$size}{$path}";
    }
}