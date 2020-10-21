<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GameController
 * @package App\Controller
 * @Route("/game", name="game_")
 */
class GameController extends AbstractController
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
        $game = new Game();
        $form = $this->createForm(GameFormType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_list');
        }

        return $this->render('game/create.html.twig', [
            'edit' => false,
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function editAction(Request $request, Game $game): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(GameFormType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_list');
        }

        return $this->render('game/create.html.twig', [
            'edit' => true,
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete_restore/{id}", name="delete_restore")
     * @param Game $game
     * @return Response
     */
    public function deleteRestoreAction(Game $game): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $game->setDeleted(!$game->getDeleted());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->redirectToRoute('game_list');
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
        $games = $entityManager->getRepository('App:Game')->findAll();

        return $this->render('game/list.html.twig', [
            'games' => $games,
        ]);
    }
}
