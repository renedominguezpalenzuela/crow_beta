<?php

namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Building;
use App\Entity\BuildingType;
use App\Entity\UnitType;
use App\Entity\Troop;
use App\Entity\TroopBuilding;

/**
 * Class CreateInitialUserData
 *
 *
 */

//Ultima version 24-08-2019  2:15 pm
class Datos
{
    private $em=null;
   
    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;     

    }


    public function borrarDatos(){

        echo "sss";
    }

    public function addKingdom($kingdom_name, $image, $id_kingdom_boss, $color_class, $main_castle_id) {


    }


    public function addUsuario($name, $user_name, $password, $email, $role, $kingdom_id) {


    }
}
