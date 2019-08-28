<?php

namespace App\Repository;

use App\Entity\Kingdom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Kingdom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kingdom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kingdom[]    findAll()
 * @method Kingdom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KingdomRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Kingdom::class);
    }

    public function BorrarAllRecords(){
        return $this->getEntityManager()
                            ->createQuery('DELETE App\Entity\Kingdom')                             
                            ->getResult();

       }

    // /**
    //  * @return Kingdom[] Returns an array of Kingdom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Kingdom
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
