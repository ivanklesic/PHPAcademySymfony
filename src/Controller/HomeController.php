<?php

namespace App\Controller;

use App\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $games = $entityManager->getRepository('App:Game')->getActive();
        $genres = $entityManager->getRepository('App:Genre')->getActive();

        return $this->render('home/index.html.twig', [
            'games' => $games,
            'genres' => $genres
        ]);
    }

    /**
     * @Route("/home/{id}", name="home_genre")
     * @param Genre $genre
     * @return RedirectResponse|Response
     */
    public function indexGenre(Genre $genre)
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $games = $entityManager->getRepository('App:Game')->getGamesOfGenre($genre);
        $genres = $entityManager->getRepository('App:Genre')->getActive();

        return $this->render('home/index.html.twig', [
            'games' => $games,
            'genres' => $genres
        ]);
    }
}
