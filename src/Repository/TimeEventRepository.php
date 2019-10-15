<?php

namespace App\Repository;

use App\Entity\TimeEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TimeEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeEvent[]    findAll()
 * @method TimeEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeEventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TimeEvent::class);
    }

    // /**
    //  * @return TimeEvent[] Returns an array of TimeEvent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeEvent
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
