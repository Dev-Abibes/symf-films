<?php

// src/Service/TMDbService.php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TMDbService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.themoviedb.org/3/',
            'headers' => [
                'Authorization' => 'Bearer ' . $_ENV['TMDB_API_TOKEN'],
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function getConfiguration(): array
    {
        try {
            $response = $this->client->request('GET', 'configuration');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception("Failed to retrieve TMDb configuration: " . $e->getMessage());
        }
    }

    public function getPopularMovies(): array
    {
        try {
            $response = $this->client->request('GET', 'movie/popular');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \Exception("Failed to retrieve popular movies: " . $e->getMessage());
        }
    }

    // Ajoute d'autres méthodes pour d'autres appels API si nécessaire
}
