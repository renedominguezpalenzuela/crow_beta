<?php

namespace App\Repository;

use App\Entity\WorldTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WorldTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorldTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorldTime[]    findAll()
 * @method WorldTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorldTimeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorldTime::class);
    }

    // /**
    //  * @return WorldTime[] Returns an array of WorldTime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorldTime
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
