<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TmdbClient
{
    private HttpClientInterface $tmdbClient;
    private CacheInterface $cache;

    public function __construct(HttpClientInterface $tmdbClient, CacheInterface $cache)
    {
        $this->tmdbClient = $tmdbClient;
        $this->cache = $cache;
    }

    public function popular(): array
    {
        return $this->cache->get('popular', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            $response = $this->tmdbClient->request('GET', '/3/movie/popular', [
                'query' => [
                    'language' => 'fr-FR'
                ]
            ]);

            return $response->toArray();
        });
    }
}
