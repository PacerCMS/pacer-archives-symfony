<?php

namespace App\Repository;

use App\Entity\TimelineEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimelineEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineEvent[]    findAll()
 * @method TimelineEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimelineEvent::class);
    }

    public function findBetween(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('t')
            ->where('t.startDate >= :start')
            ->andWhere('t.startDate <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('t.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
