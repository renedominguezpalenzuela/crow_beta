<?php

namespace App\Repository;

use App\Entity\Building;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Building|null find($id, $lockMode = null, $lockVersion = null)
 * @method Building|null findOneBy(array $criteria, array $orderBy = null)
 * @method Building[]    findAll()
 * @method Building[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuildingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Building::class);
    }

    public function findCastle($userId)
    {
        return $this->createQueryBuilder('b')
            ->join('b.user', 'u')
            ->join('b.buildingType', 'bt')
            ->where('u.id = :userId')
            ->orderBy('b.id', 'ASC')
            ->setParameter('userId', $userId)
            ->getQuery()->getOneOrNullResult();
    }


    public function BuscarEdificiosEnemigos($kingdom_id){
        return $this->getEntityManager()
                             ->createQuery(
                                        '
                                               SELECT b
                                               FROM App\Entity\Building b
                                               WHERE b.kingdom!=:kindom_id
                                        '
                             )
                             ->setParameter('kindom_id', $kingdom_id)
                            ->getResult() 
                         ;
       }

       
}
