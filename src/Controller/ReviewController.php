<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ReviewController
 * @package App\Controller
 * @Route("/review", name="review_")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/create/{id}", name="create")
     * @param Request $request
     * @param Game $game
     * @return Response
     */
    public function createAction(Request $request, Game $game): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $previousReview = $entityManager->getRepository('App:Review')->findOneBy([
            'user' => $currentUser,
            'game' => $game
        ]);

        if($previousReview)
        {
            return $this->redirectToRoute('review_edit', ['id' => $previousReview->getId()]);
        }

        $review = new Review();
        $form = $this->createForm(ReviewFormType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $review->setGame($game);
            $review->setUser($currentUser);
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('review/create.html.twig', [
            'edit' => false,
            'form' => $form->createView(),
            'game' => $game
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Review $review
     * @return Response
     */
    public function editAction(Request $request, Review $review): Response
    {
        if(!$this->isGranted('edit', $review))
        {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ReviewFormType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('review/create.html.twig', [
            'edit' => true,
            'form' => $form->createView(),
            'game' => $review->getGame()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Review $review
     * @return Response
     */
    public function deleteAction(Review $review): Response
    {
        if(!$this->isGranted('delete', $review))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($review);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/list/{my}", name="list")
     * @param $my
     * @return Response
     */
    public function listAction($my): Response
    {
        if(!$this->isGranted('ROLE_ADMIN') && !$my || !$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $reviewRepository = $entityManager->getRepository('App:Review');

        $reviews = $my ? $reviewRepository->findBy(['user' => $this->getUser()]) : $reviewRepository->findAll();

        return $this->render('review/list.html.twig', [
            'reviews' => $reviews,
            'my' => $my,
            'game' => false
        ]);
    }

    /**
     * @Route("/game/{id}", name="game")
     * @param Game $game
     * @return Response
     */
    public function gameAction(Game $game): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $reviewRepository = $entityManager->getRepository('App:Review');

        $reviews = $reviewRepository->findBy(['game' => $game]);

        return $this->render('review/list.html.twig', [
            'reviews' => $reviews,
            'my' => false,
            'game' => $game
        ]);
    }

    /**
     * @Route("/details/{id}", name="details")
     * @param Review $review
     * @return Response
     */
    public function detailsAction(Review $review): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        return $this->render('review/details.html.twig', [
            'review' => $review,
        ]);
    }
}
