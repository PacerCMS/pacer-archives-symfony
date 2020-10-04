<?php

namespace App\Repository;

use App\Entity\Issue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Issue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Issue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Issue[]    findAll()
 * @method Issue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IssueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issue::class);
    }

    public function getTotalIssueCount()
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id) as issueCount')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findPreviousIssue(Issue $issue)
    {
        return $this->createQueryBuilder('i')
            ->where('i.issueDate < :issueDate')
            ->setParameter('issueDate', $issue->getIssueDate())
            ->orderBy('i.issueDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextIssue(Issue $issue)
    {
        return $this->createQueryBuilder('i')
            ->where('i.issueDate > :issueDate')
            ->setParameter('issueDate', $issue->getIssueDate())
            ->orderBy('i.issueDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getIssuesByYear(int $year)
    {
        return $this->createQueryBuilder('i')
            ->where('i.issueDate >= :start')
            ->andWhere('i.issueDate <= :end')
            ->setParameter('start', date('Y-m-d', strtotime("january 1 $year")))
            ->setParameter('end', date('Y-m-d', strtotime("december 31 $year")))
            ->orderBy('i.issueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBetween(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.volume', 'v')
            ->addSelect('v')
            ->where('i.issueDate >= :start')
            ->andWhere('i.issueDate <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('i.issueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
