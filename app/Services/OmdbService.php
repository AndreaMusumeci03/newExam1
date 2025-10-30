<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OmdbService
{
    private $apiKey;
    private $baseUrl = 'https://www.omdbapi.com/';

    public function __construct()
    {
        $this->apiKey = env('OMDB_API_KEY');
    }

    public function searchByTitle($title, $page = 1)
    {
        if (empty($this->apiKey)) {
            return [];
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get($this->baseUrl, [
                    'apikey' => $this->apiKey,
                    's' => $title,
                    'page' => $page,
                    'type' => 'movie',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['Response']) && $data['Response'] === 'True') {
                    return $data['Search'] ?? [];
                }
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Errore OMDb searchByTitle: ' . $e->getMessage());
            return [];
        }
    }

    public function getByImdbId($imdbId)
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->get($this->baseUrl, [
                    'apikey' => $this->apiKey,
                    'i' => $imdbId,
                    'plot' => 'full',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['Response']) && $data['Response'] === 'True') {
                    return $data;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Errore OMDb getByImdbId: ' . $e->getMessage());
            return null;
        }
    }

    // ✅ AGGIORNATO: Più film e categorie diverse
    public function getPopularMovies()
    {
        $popularTitles = [
            // Classici
            'The Shawshank Redemption',
            'The Godfather',
            'The Dark Knight',
            'Pulp Fiction',
            'Forrest Gump',
            'Fight Club',
            'The Matrix',
            'Goodfellas',
            'Se7en',
            'The Silence of the Lambs',
            
            // Fantascienza
            'Inception',
            'Interstellar',
            'Blade Runner',
            'The Terminator',
            'Aliens',
            'Back to the Future',
            'E.T.',
            
            // Avventura
            'The Lord of the Rings',
            'Star Wars',
            'Indiana Jones',
            'Jurassic Park',
            'Pirates of the Caribbean',
            
            // Drammi
            'The Green Mile',
            'Schindler\'s List',
            'Good Will Hunting',
            'A Beautiful Mind',
            'The Pianist',
            
            // Azione
            'Die Hard',
            'Mad Max',
            'John Wick',
            'Gladiator',
            'The Bourne Identity',
            
            // Supereroi
            'The Avengers',
            'Iron Man',
            'The Dark Knight Rises',
            'Spider-Man',
            'Black Panther',
            
            // Animazione
            'Toy Story',
            'The Lion King',
            'Finding Nemo',
            'WALL-E',
            'Up',
            
            // Recenti Popolari
            'Avatar',
            'Titanic',
            'Joker',
            'Parasite',
            'Dune',
        ];

        $movies = [];

        foreach ($popularTitles as $title) {
            $results = $this->searchByTitle($title);
            if (!empty($results)) {
                $movies[] = $results[0];
            }
            
            usleep(250000); // 0.25 secondi (leggermente più veloce)
            
            // ✅ Limite di sicurezza per non superare il rate limit API
            if (count($movies) >= 50) {
                break;
            }
        }
        
        return $movies;
    }

    // ✅ NUOVO: Cerca film per genere (funziona solo con ricerche specifiche)
    public function searchByGenre($genre, $year = null)
    {
        // Cerca film popolari di un genere specifico
        $keywords = [
            'action' => ['Mission Impossible', 'Fast Furious', 'James Bond', 'Rambo'],
            'comedy' => ['Hangover', 'Superbad', 'Anchorman', 'Bridesmaids'],
            'horror' => ['Halloween', 'Scream', 'The Conjuring', 'Get Out'],
            'romance' => ['Titanic', 'Notebook', 'La La Land', 'Pretty Woman'],
            'thriller' => ['Gone Girl', 'Shutter Island', 'Prisoners', 'Zodiac'],
        ];

        $results = [];
        
        if (isset($keywords[$genre])) {
            foreach ($keywords[$genre] as $keyword) {
                $search = $this->searchByTitle($keyword);
                if (!empty($search)) {
                    $results = array_merge($results, $search);
                }
            }
        }

        return $results;
    }
}