<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamController
 * @package App\Controller
 * @Route("/team", name="team_")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }
        $team = new Team();
        $form = $this->createForm(TeamFormType::class, $team, [
            'edit' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $team->setLeader($this->getUser());
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('team/create.html.twig', [
            'edit' => false,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Team $team
     * @return Response
     */
    public function editAction(Request $request, Team $team): Response
    {
        if(!$this->isGranted('edit', $team))
        {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(TeamFormType::class, $team, [
            'edit' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('team/create.html.twig', [
            'edit' => true,
            'form' => $form->createView(),
        ]);
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
        $teamRepository = $entityManager->getRepository('App:Team');

        $teams = $my ? $teamRepository->getTeamsOfUser($this->getUser()) : $teamRepository->findAll();

        return $this->render('team/list.html.twig', [
            'teams' => $teams,
            'my' => $my
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Team $team
     * @return Response
     */
    public function deleteAction(Team $team): Response
    {
        if(!$this->isGranted('delete', $team))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($team);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/details/{id}", name="details")
     * @param Team $team
     * @return Response
     */
    public function detailsAction(Team $team) : Response
    {
        if(!$this->isGranted('details', $team))
        {
            return $this->redirectToRoute('home');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $eventRepository = $entityManager->getRepository('App:Event');
        $events = $eventRepository->getActiveAndPendingEventsOfTeam($team);

        return $this->render('team/details.html.twig', [
            'team' => $team,
            'events' => $events
        ]);
    }
}
