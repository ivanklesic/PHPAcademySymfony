<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GameFormType;
use App\Service\FileUploader;
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
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function createAction(Request $request, FileUploader $fileUploader): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $game = new Game();
        $form = $this->createForm(GameFormType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'game');
                $game->setImageUrl($imageFileName);
            }

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
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function editAction(Request $request, Game $game, FileUploader $fileUploader): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(GameFormType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form['image']->getData();
            if ($imageFile) {
                if($game->getImageUrl())
                {
                    unlink('images/game/' . $game->getImageUrl());
                }
                $imageFileName = $fileUploader->upload($imageFile, 'game');
                $game->setImageUrl($imageFileName);
            }

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

    /**
     * @Route("/compare/{id}", name="compare")
     * @param Game $game
     * @return Response
     */
    public function compareAction(Game $game): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        /** @var User $user */
        $user = $this->getUser();
        $compareError = null;

        if(!$user || $user->getCpuFreq() === null || $user->getCpuCores() === null || $user->getGpuVram() === null || $user->getRam() === null || $user->getStorageSpace() === null)
        {
            $compareError = 'Your PC specs are not complete';
        }

        return $this->render('game/compare.html.twig', [
            'game' => $game,
            'user' => $user,
            'error' => $compareError
        ]);

    }

}
