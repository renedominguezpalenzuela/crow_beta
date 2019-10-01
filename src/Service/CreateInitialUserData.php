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

//Ultima version 22-06-2019  2:15 pm
class CreateInitialUserData
{

    private $em=null;
    private $team=null;
    private $kingdom=null;
    private $config=null;
    private $id_user_boss=0;

    public function __construct(EntityManagerInterface $entityManager, GlobalConfig $globalconfig)
    {
        $this->em = $entityManager;
        $this->config= $globalconfig;
    }

    //Crear datos de un nuevo usuario
    public function crear(User $user)
    {


        //Setear recursos del user
        $gold=$this->config->getUserInitialGold();
        $user->setGold($gold); 

        //obtengo datos del team y el kingdom del usuario
        $this->getKingdomData($user);

        //Creando el castillo
        $this->createCasttle($user);

        //Crear la barraca
        $this->crearBarrack($user);

        //Crear las tropas
        $this->CreateTroops($user);

     

        return;

    }

    public function getKingdomData(User $user)
    {
        //1)busco kingdom del user
        //el kingdom se escoge al crear el usuario
        //$this->team = $this->em->getRepository(Team::class)->findOneBy(['user' => $user->getID()]);
        $this->kingdom = $user->getKingdom();

        //2) busco el boss del team (es el user duenno del castillo)
        $this->id_user_boss = $this->kingdom->getIdKingdomBoss();

    }

    //----------------------------------------------------------------------------------------------------
    // Crea castillo en tabla buildings
    //----------------------------------------------------------------------------------------------------
    public function createCasttle(User $user)
    {
        //Verificar si el team al que pertence el usuario tiene castillo
        //si no tiene, se crea uno
        //tambien se puede al crear el team, crear los castillos
        //al crear un usuario del team, buscar si existe un boss, si no existe se pone al usuario como boss

        //si no existe boss ??--> crear el castillo

        //busco el id de los tipos de castillo (TODO: crear un ciclo filtrar por name=castle)
        $castle_type_lv1 = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level' => 1]);
        $castle_type_lv2 = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level' => 2]);

        //busco si existe un castillo lv1 para el team de ese usuario
        $castillo = $this->em->getRepository(Building::class)->findOneBy(['kingdom' => $this->kingdom, 'buildingType' => $castle_type_lv1]);

        //busco si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
        if ($castillo == null) {
            $castillo = $this->em->getRepository(Building::class)->findOneBy(['kingdom' => $this->kingdom, 'buildingType' => $castle_type_lv2]);
        }

        //si no existe castillo lvl1 ni lv2 lo creo
        if ($castillo == null) {

            $castillo = new Building();
            $castillo->setUser($user);
            $castillo->setName2($castle_type_lv1->getName());
          
            $castillo->setBuildingType($castle_type_lv1);

            $castillo->setDefenseRemaining($castle_type_lv1->getDefense());
            $castillo->setKingdom($this->kingdom);

          //  $this->em->persist($castillo);

           
            $this->em->persist($castillo);
            $this->em->flush();

         

            $this->kingdom->setMainCastleId($castillo->getId());
            $this->kingdom->setIdKingdomBoss($user->getId());

            $this->em->persist($this->kingdom);

            $this->em->flush();



            //setear el boss del castillo
        }

        $castillo->setName2($castle_type_lv1->getName().':'.$castillo->getId());
        $this->em->persist($castillo);
        $this->em->flush();

    }

    public function crearBarrack(User $user)
    {

        //Crear barracks
        $barrack_type = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'Barrack', 'level' => 1]);
        $barrack = new Building();

        $barrack->setUser($user);
        $barrack->setBuildingType($barrack_type);
       
        $barrack->setKingdom($this->kingdom);
        $barrack->setName2($barrack_type->getName());

        $barrack->setDefenseRemaining($barrack_type->getDefense());

        $this->em->persist($barrack);
        $this->em->flush();


        $barrack->setName2($barrack_type->getName().':'.$barrack->getId());
        $this->em->persist($barrack);
        $this->em->flush();

    }

    //Crear tropas ubicarlas inicialmente en la barraca
    private function CreateTroops(User $user)
    {

        //Busco todos los troops de level 1
        $troops_type = $this->em->getRepository(UnitType::class)->findBy(['level' => '1']);

        //Buscar la barraca
        $barrack_type = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'Barrack', 'level' => 1]);
        $barraca = $this->em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType' => $barrack_type]);

        foreach ($troops_type as $untrooptype) {
            $troop = new Troop();
            $troop->setUser($user);
            $troop->setLevel($untrooptype->getLevel());
            $troop->setTotal($untrooptype->getTotalInitial());
            $troop->setAttack($untrooptype->getAttack());
            $troop->setDefense($untrooptype->getDefense());
            $troop->setDamage($untrooptype->getDamage());
            $troop->setSpeed($untrooptype->getSpeed());
            $troop->setUnitType($untrooptype);

            $this->em->persist($troop);

            $tropa_ubicada = new TroopBuilding();
            $tropa_ubicada->setTroops($troop);
            $tropa_ubicada->setBuilding($barraca);
            $tropa_ubicada->setTotal($troop->getTotal());

            $this->em->persist($tropa_ubicada);

        }

        $this->em->flush();

    }
}

