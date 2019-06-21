<?php

namespace App\Repository;

use App\Entity\Bitacora;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bitacora|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bitacora|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bitacora[]    findAll()
 * @method Bitacora[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BitacoraRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bitacora::class);
    }

    // /**
    //  * @return Bitacora[] Returns an array of Bitacora objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bitacora
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
