<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Team;
use App\Form\EventFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EventController
 * @package App\Controller
 * @Route("/event", name="event_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/create/{id}", name="create")
     * @param Request $request
     * @param Team $team
     * @return Response
     */
    public function createAction(Request $request, Team $team): Response
    {
        if(!$this->isGranted('events', $team))
        {
            return $this->redirectToRoute('home');
        }

        $event= new Event();

        $form = $this->createForm(EventFormType::class, $event, [
            'edit' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $event->setTeam($team);
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('team_details', ['id' => $team->getId()]);
        }

        return $this->render('event/create.html.twig', [
            'edit' => false,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Event $event
     * @return Response
     */
    public function deleteAction(Event $event): Response
    {
        if(!$this->isGranted('events', $event->getTeam()))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('team_details', ['id' => $event->getTeam()->getId()]);
    }
}
