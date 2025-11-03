<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    protected string $base = 'https://api.themoviedb.org/3';
    protected string $token;
    protected string $lang;
    protected $verify; // string (path) | bool | null

    public function __construct()
    {
        $this->token = (string) config('services.tmdb.token');
        $this->lang  = (string) config('services.tmdb.lang', 'it-IT');

        // Se impostato un path CA, usalo. Altrimenti usa il flag booleano verify.
        $caPath = config('services.tmdb.ca');           // es. E:\xampp\php\extras\ssl\cacert.pem
        $verify = config('services.tmdb.verify', false); // true|false

        $this->verify = $caPath ?: $verify;
    }

    protected function request()
    {
        $options = [];
        if ($this->verify !== null) {
            $options['verify'] = $this->verify;
        }

        return Http::withoutOptions($options)
            ->withToken($this->token)
            ->acceptJson();
    }

    protected function get(string $path, array $params = []): array
    {
        $response = $this->request()
            ->get($this->base . $path, array_merge(['language' => $this->lang], $params))
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