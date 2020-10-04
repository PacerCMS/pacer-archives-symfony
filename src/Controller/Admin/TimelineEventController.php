<?php

namespace App\Controller\Admin;

use App\Entity\TimelineEvent;
use App\Form\TimelineEventType;
use App\Repository\TimelineEventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/timeline/event")
  * @IsGranted("ROLE_ADMIN")
 */
class TimelineEventController extends AbstractController
{
    /**
     * @Route("/", name="timeline_event_index", methods={"GET"})
     */
    public function index(TimelineEventRepository $timelineEventRepository): Response
    {
        return $this->render('admin/timeline_event/index.html.twig', [
            'timeline_events' => $timelineEventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="timeline_event_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $timelineEvent = new TimelineEvent();
        $form = $this->createForm(TimelineEventType::class, $timelineEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($timelineEvent);
            $entityManager->flush();

            return $this->redirectToRoute('timeline_event_index');
        }

        return $this->render('admin/timeline_event/new.html.twig', [
            'timeline_event' => $timelineEvent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="timeline_event_show", methods={"GET"})
     */
    public function show(TimelineEvent $timelineEvent): Response
    {
        return $this->render('admin/timeline_event/show.html.twig', [
            'timeline_event' => $timelineEvent,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="timeline_event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TimelineEvent $timelineEvent): Response
    {
        $form = $this->createForm(TimelineEventType::class, $timelineEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('timeline_event_index');
        }

        return $this->render('admin/timeline_event/edit.html.twig', [
            'timeline_event' => $timelineEvent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="timeline_event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TimelineEvent $timelineEvent): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timelineEvent->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($timelineEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('timeline_event_index');
    }
}
