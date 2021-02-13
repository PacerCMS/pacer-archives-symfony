<?php

namespace App\Controller\Admin;

use App\Entity\Issue;
use App\Form\IssueType;
use App\Repository\IssueRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/issue")
 * @IsGranted("ROLE_ADMIN")
 */
class IssueController extends AbstractController
{
    /**
     * @Route("/", name="issue_index", methods={"GET"})
     */
    public function index(IssueRepository $issueRepository): Response
    {
        return $this->render('admin/issue/index.html.twig', [
            'issues' => $issueRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="issue_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $issue = new Issue();
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            return $this->redirectToRoute('issue_index');
        }

        return $this->render('admin/issue/new.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="issue_show", methods={"GET"})
     */
    public function show(Issue $issue): Response
    {
        return $this->render('admin/issue/show.html.twig', [
            'issue' => $issue,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="issue_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Issue $issue): Response
    {
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($request->query->get('return') == 'public') {
                return $this->redirectToRoute('issue', ['issueDate' => $issue->getIssueDate()->format('Y-m-d')]);
            }
            return $this->redirectToRoute('issue_index');
        }

        return $this->render('admin/issue/edit.html.twig', [
            'issue' => $issue,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="issue_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Issue $issue): Response
    {
        if ($this->isCsrfTokenValid('delete'.$issue->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($issue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('issue_index');
    }
}
