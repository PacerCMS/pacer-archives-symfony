<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Form\ArticleSearchType;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search")
     */
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request)
    {
        $searchForm = $this->createForm(ArticleSearchType::class, null, [
            'method' => 'GET',
            'action' => $this->generateUrl('search'),
            's' => $request->query->get('s'),
            'index' => $request->query->get('index', 'content')
        ]);

        $pagination = false;

        if ($request->query->get('s')) {
            if ($request->query->get('index') == 'author') {
                $query = $articleRepository->searchAuthor($request->query->get('s'));
            } else {
                $query = $articleRepository->searchContent($request->query->get('s'));
            }
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10,
                ['wrap-queries' => true]
            );
        }

        return $this->render('search/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination
        ]);
    }
}
