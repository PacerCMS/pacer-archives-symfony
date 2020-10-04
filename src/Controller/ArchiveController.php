<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

use Aws\S3\S3Client;

use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\IssueRepository;
use App\Repository\VolumeRepository;
use App\Entity\TimelineEvent;

/**
 * @Route("/archive")
 */
class ArchiveController extends AbstractController
{

    const START_YEAR = 1928;

    /**
     * @Route("/", name="archive")
     */
    public function index(IssueRepository $issueRepository, VolumeRepository $volumeRepository)
    {
        $volumes = $volumeRepository->findAllCurrentVolumes();
        $issues = $issueRepository->findAll();

        $years = range(self::START_YEAR, date('Y'));
        $issue_counts_by_year = [];
        foreach ($years as $year) {
            $issue_counts_by_year[$year] = 0;
        }
        foreach ($issues as $issue) {
            $issue_counts_by_year[$issue->getIssueDate()->format('Y')]++;
        }

        return $this->render('archive/index.html.twig', [
            'issue_counts_by_year' => $issue_counts_by_year,
            'volumes' => $volumes,
            'years' => $years,
            'opengraph' => [
                'title' => 'The Pacer Archives',
                'description' => 'A collection of student newspaper issues since 1928.'
            ]
        ]);
    }

    /**
     * @Route("/volume/{volumeNumber}", name="volume", requirements={"volumeNumber"="\d+"})
     */
    public function volume(VolumeRepository $volumeRepository, int $volumeNumber)
    {
        $volume = $volumeRepository->findOneBy(['volumeNumber' => $volumeNumber]);

        if (!$volume) {
            throw $this->createNotFoundException('No matching volume found.');
        }

        $previousVolume = $volumeRepository->findPreviousVolume($volume);
        $nextVolume = $volumeRepository->findNextVolume($volume);

        return $this->render('archive/volume.html.twig', [
            'volume' => $volume,
            'previousVolume' => $previousVolume,
            'nextVolume' => $nextVolume,
            'opengraph' => [
                'title' => $volume,
                'description' => 'Issues that appeared in ' . $volume,
                'image' => 'https://archive.org/services/img/' . $volume->getCoverIssue()->getArchiveKey()
            ]
        ]);
    }

    /**
     * @Route("/year/{year}", name="year", requirements={"year"="[1|2][0|9][0-9][0-9]"})
     */
    public function year(IssueRepository $issueRepository, int $year)
    {
        $issues = $issueRepository->getIssuesByYear($year);

        if ($year < self::START_YEAR || $year > (date('Y'))) {
            throw $this->createNotFoundException('No matching year found.');
        }

        return $this->render('archive/year.html.twig', [
            'issues' => $issues,
            'hasUTMDigitalArchives' => (bool) count(array_filter($issues, function ($issue) {
                return (bool) $issue->getUtmDigitalArchiveUrl();
            })),
            'year' => $year,
            'previousYear' => ($year > self::START_YEAR) ? $year - 1 : false,
            'nextYear' => ($year < (int) date('Y')) ? $year + 1 : false,
            'opengraph' => [
                'title' => 'The Pacer - ' . $year,
                'description' => 'Issues of The Volette and The Pacer from ' . $year . '.'
            ]
        ]);
    }

    /**
     * @Route("/issue/{issueDate}", name="issue", requirements={"issueDate"="([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])"})
     * @Route("/issue-{issueDate}", requirements={"issueDate"="([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])"})
     */
    public function issue(IssueRepository $issueRepository, string $issueDate)
    {
        $issue = $issueRepository->findOneBy(['issueDate' => new \DateTime($issueDate)]);

        if (!$issue) {
            throw $this->createNotFoundException('No matching issue found.');
        }

        $previousIssue = $issueRepository->findPreviousIssue($issue);
        $nextIssue = $issueRepository->findNextIssue($issue);

        return $this->render('archive/issue.html.twig', [
            'issue' => $issue,
            'previousIssue' => $previousIssue,
            'nextIssue' => $nextIssue,
            'opengraph' => [
                'title' => sprintf(
                    'The %s - %s',
                    ucwords($issue->getVolume()->getNameplateKey()),
                    $issue->getIssueDate()->format('F j, Y')
                ),
                'description' => sprintf(
                    'The %s issue of The %s, the student newspaper at the University of Tennessee at Martin.',
                    $issue->getIssueDate()->format('F j, Y'),
                    ucwords($issue->getVolume()->getNameplateKey())
                ),
                'image' => 'https://archive.org/services/img/' . $issue->getArchiveKey()
            ]
        ]);
    }

    /**
     * @Route("/timeline-data-source", name="timeline_data_source")
     */
    public function timelineDataSource(Request $request, IssueRepository $issueRepository)
    {
        $startDate = new \DateTime($request->get('start_date', date('Y-m-d', strtotime('January 1, 1928'))));
        $endDate = new \DateTime($request->get('end_date', date('Y-m-d')));
        $output = [];

        $entityManager = $this->getDoctrine()->getManager();

        // Newspaper Issues
        $issues = $issueRepository->findBetween($startDate, $endDate);
        foreach ($issues as $issue) {
            $output[] = $issue->getAsTimelineEventArray($this->generateUrl('issue', [
                'issueDate' => $issue->getIssueDate()->format('Y-m-d')
            ]));
        }

        // Timeline Events
        $eventRepository = $entityManager->getRepository(TimelineEvent::class);
        $events = $eventRepository->findBetween($startDate, $endDate);
        foreach ($events as $event) {
            $output[] = $event->getAsTimelineEventArray();
        }

        return $this->json([
            'events' => $output
        ]);
    }

    /**
     * @Route("/article/{slug}/{id}", name="article", requirements={"id"="\d+"})
     */
    public function article(ArticleRepository $articleRepository, string $slug, int $id)
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('No matching article found.');
        }

        // Prevent slug manipulation
        if ($slug != $article->getSlug()) {
            return $this->redirectToRoute('article', [
                'id' => $article->getId(),
                'slug' => $article->getSlug()
            ], 301);
        }

        if (count($article->getImages())) {
            $articleImage = $this->generateUrl(
                's3_proxy',
                [
                    'id' => $article->getImages()->first()->getId()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        } else {
            // No image attached, use issue cover if set
            if ($article->getIssue()) {
                $articleImage = 'https://archive.org/services/img/' . $article->getIssue()->getArchiveKey();
            } else {
                $articleImage = false;
            }
        }

        // Determine nameplateKey to use for opengraph data
        if ($article->getIssue()) {
            $nameplateKey = $article->getIssue()->getVolume()->getNameplateKey();
        } else {
            $nameplateKey = 'pacer';
        }

        return $this->render('archive/article.html.twig', [
            'article' => $article,
            'opengraph' => [
                'title' => sprintf(
                    '%s - The %s',
                    $article->getHeadline(),
                    ucwords($nameplateKey)
                ),
                'description' => $article->getArticleBody(), // Truncated in Twig template
                'image' => $articleImage
            ]
        ]);
    }

    /**
     * S3 Proxy
     *
     * @Route("/image/{id}", name="s3_proxy", requirements={"id"="\d+"})
     */
    public function s3Proxy(ImageRepository $imageRepository, S3Client $s3Client, $id)
    {
        $image = $imageRepository->find($id);

        if (!$image) {
            throw $this->createNotFoundException('No matching image found.');
        }

        try {
            $object = $s3Client->getObject([
                'Bucket' => 'pacer-archives',
                'Key'    => $image->getPath()
            ]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            throw $this->createNotFoundException('Unable to load image.');
        }

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            basename($image->getPath())
        );

        $response = new Response($object['Body']);
        $response->headers->set('Content-Type', $object['ContentType']);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @Route("/legacy-issue/{issueDate}", name="legacy_issue", requirements={"issueDate"="([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])"})
     */
    public function legacyIssue(IssueRepository $issueRepository, string $issueDate)
    {
        $issue = $issueRepository->findOneBy(['issueDate' => new \DateTime($issueDate)]);

        if (!$issue) {
            throw $this->createNotFoundException('Could not locate legacy issue.');
        }

        // Redirect to new route
        return $this->redirectToRoute('issue', [
            'issueDate' => $issue->getIssueDate()->format('Y-m-d')
        ], 301);
    }

    /**
     * Handle PacerCMS (Legacy) Article Links
     *
     * @Route("/legacy-article/{id}", name="legacy_article", requirements={"id"="\d+"})
     */
    public function legacyArticle(ArticleRepository $articleRepository, int $id)
    {
        $article = $articleRepository->findOneBy(['legacyId' => $id]);

        if (!$article) {
            throw $this->createNotFoundException('Could not locate legacy article.');
        }

        // Redirect to new route
        return $this->redirectToRoute('article', [
            'id' => $article->getId(),
            'slug' => $article->getSlug()
        ], 301);
    }
}
