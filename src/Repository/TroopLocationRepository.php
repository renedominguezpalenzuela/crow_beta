<?php

namespace App\Repository;

use App\Entity\TroopLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TroopLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TroopLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TroopLocation[]    findAll()
 * @method TroopLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TroopLocationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TroopLocation::class);
    }

    // /**
    //  * @return TroopLocation[] Returns an array of TroopLocation objects
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
    public function findOneBySomeField($value): ?TroopLocation
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
