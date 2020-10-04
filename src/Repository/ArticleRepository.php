<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function searchContent(string $searchTerm)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('MATCH (a.articleBody, a.headline, a.alternativeHeadline) AGAINST (:searchTerm) AS score')
            ->setParameter('searchTerm', $searchTerm)
            ->having('score > 0')
            ->orderBy('score', 'DESC')
            ->getQuery()
        ;
    }

    public function searchAuthor(string $searchTerm)
    {
        return $this->createQueryBuilder('a')
            ->addSelect('MATCH (a.author_byline, a.contributor_byline) AGAINST (:searchTerm) AS score')
            ->setParameter('searchTerm', $searchTerm)
            ->having('score > 0')
            ->orderBy('score', 'DESC')
            ->getQuery()
        ;
    }
}
