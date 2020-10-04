<?php

namespace App\Repository;

use App\Entity\Volume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Volume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volume[]    findAll()
 * @method Volume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volume::class);
    }

    public function findAllCurrentVolumes()
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.issues', 'i')
            ->addSelect('i')
            ->andWhere('v.volumeStartDate < :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('v.volumeStartDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPreviousVolume(Volume $volume)
    {
        return $this->createQueryBuilder('v')
            ->where('v.volumeStartDate < :volumeStartDate')
            ->setParameter('volumeStartDate', $volume->getVolumeStartDate())
            ->orderBy('v.volumeStartDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextVolume(Volume $volume)
    {
        return $this->createQueryBuilder('v')
            ->where('v.volumeStartDate > :volumeStartDate')
            ->andWhere('v.volumeStartDate < :today')
            ->setParameter('volumeStartDate', $volume->getVolumeEndDate())
            ->setParameter('today', new \DateTime())
            ->orderBy('v.volumeStartDate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
