<?php

// src/Controller/MovieController.php

namespace App\Controller;

use App\Service\TMDbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    private TMDbService $tmdbService;

    public function __construct(TMDbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    #[Route('/movies', name: 'movies_list')]
    public function list(): Response
    {
        // Récupère la configuration des images
        $config = $this->tmdbService->getConfiguration();
        $baseImageUrl = $config['images']['base_url'] . 'w500'; // Taille d'image `w500` pour les posters

        // Récupère la liste des films populaires
        $movies = $this->tmdbService->getPopularMovies();

        return $this->render('movies/list.html.twig', [
            'movies' => $movies['results'], // Liste des films
            'baseImageUrl' => $baseImageUrl // URL de base pour les images
        ]);
    }

    #[Route('/movies/configuration', name: 'movies_configuration')]
    public function configuration(): JsonResponse
    {
        $config = $this->tmdbService->getConfiguration();
        return new JsonResponse($config);
    }

    #[Route('/movies/popular', name: 'movies_popular')]
    public function popular(): JsonResponse
    {
        $movies = $this->tmdbService->getPopularMovies();
        return new JsonResponse($movies);
    }
}
