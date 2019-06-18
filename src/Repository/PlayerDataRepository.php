<?php

namespace App\Repository;

use App\Entity\PlayerData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PlayerData|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerData|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerData[]    findAll()
 * @method PlayerData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PlayerData::class);
    }

    // /**
    //  * @return PlayerData[] Returns an array of PlayerData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayerData
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
