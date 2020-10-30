<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GenreController
 * @package App\Controller
 * @Route("/genre", name="genre_")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $genre = new Genre();
        $form = $this->createForm(GenreFormType::class, $genre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('genre_list');
        }

        return $this->render('genre/create.html.twig', [
            'edit' => false,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Genre $genre
     * @return Response
     */
    public function editAction(Request $request, Genre $genre): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(GenreFormType::class, $genre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('genre_list');
        }

        return $this->render('genre/create.html.twig', [
            'edit' => true,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete_restore/{id}", name="delete_restore")
     * @param Genre $genre
     * @return Response
     */
    public function deleteRestoreAction(Genre $genre): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $genre->setDeleted(!$genre->getDeleted());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($genre);
        $entityManager->flush();

        return $this->redirectToRoute('genre_list');
    }

    /**
     * @Route("/list", name="list")
     * @return Response
     */
    public function listAction(): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $genres = $entityManager->getRepository('App:Genre')->findAll();

        return $this->render('genre/list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
