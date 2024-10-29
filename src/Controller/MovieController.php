<?php

// src/Controller/MovieController.php
namespace App\Controller;

use App\Service\TmdbClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    private $tmdbClient;

    public function __construct(TmdbClient $tmdbClient)
    {
        $this->tmdbClient = $tmdbClient;
    }

    /**
     * @Route("/movies", name="movies", methods={"GET"})
     */
    public function index(): Response
    {
        $movies = $this->tmdbClient->popular();

        return $this->render('movie/index.html.twig', [
            'movies' => $movies['results'],
        ]);
    }
}
