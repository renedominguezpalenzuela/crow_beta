<?php

namespace App\Service;

use App\Entity\Building;
use App\Entity\BuildingType;
use App\Entity\Kingdom;
use App\Entity\Troop;
use App\Entity\TroopBuilding;
use App\Entity\UnitType;
use App\Entity\User;
use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CreateInitialUserData
 *
 *
 */

//Ultima version 24-08-2019  2:15 pm
class Datos
{
    private $em = null;
    private $encoder = null;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $entityManager;
        $this->encoder = $encoder;
    }

    public function borrarDatos()
    {
        $this->em->getRepository(TroopBuilding::class)->BorrarAllRecords();
        $this->em->getRepository(Building::class)->BorrarAllRecords();
        $this->em->getRepository(BuildingType::class)->BorrarAllRecords();
        $this->em->getRepository(Troop::class)->BorrarAllRecords();
        $this->em->getRepository(UnitType::class)->BorrarAllRecords();
        $this->em->getRepository(User::class)->BorrarAllRecords();
        $this->em->getRepository(Kingdom::class)->BorrarAllRecords();
        $this->em->getRepository(Config::class)->BorrarAllRecords();

        
    }

    public function addAllData()
    {
        $this->addAllKingdoms();
        $this->addAllBuildingTypes();
        $this->addAllTroopsType();

        $this->addAllUsers();

        $this->addConfig();
    }

    public function addAllUsers()
    {
        //   'ROLE_ADMIN','ROLE_USER'
        $this->addUser("axl", "axl", "123", "axl@gmail.com", "ROLE_USER", "Test Kingdom");
        $this->addUser("azul", "azul", "123", "azul@gmail.com", "ROLE_USER", "Blue Kingdom");
        $this->addUser("azul1", "azul1", "123", "azul1@gmail.com", "ROLE_USER", "Blue Kingdom");
        $this->addUser("rojo", "rojo", "123", "rojo@gmail.com", "ROLE_USER", "Red Kingdom");
    }

    public function addAllKingdoms()
    {
        $this->addKingdom("Red Kingdom", "castillo1.jpg", "card-header-danger");
        $this->addKingdom("Blue Kingdom", "castillo2.jpg", "card-header-info");
        $this->addKingdom("Orange Kingdom", "castillo3.jpg", "card-header-warning");
        $this->addKingdom("White Kingdom", "castillo4.jpg", "card-header-white");
        $this->addKingdom("Green Kingdom", "castillo5.jpg", "card-header-success");
        $this->addKingdom("Test Kingdom", "castillo6.jpg", "card-header-white");
    }

    public function addAllBuildingTypes()
    {
        //$this->addBuildingType"(Castle, $cost, $level, $defense, $capacity, $minimal_unit)
        $this->addBuildingType("Castle", 0, 1, 500000, 25000, 0);
        $this->addBuildingType("Castle", 10000000, 2, 1000000, 25000, 0);
        $this->addBuildingType("Barrack", 0, 1, 0, 0, 0);
        $this->addBuildingType("Camp", 1000000, 1, 50000, 5000, 1000);
        $this->addBuildingType("Camp", 2000000, 2, 100000, 10000, 0);
    }

    public function addAllTroopsType()
    {
        // $this->addTroopType($troop_name, $level, $attack, $defense, $damage, $speed, $cost, $total_ini)
        $this->addTroopType("Archers", 1, 30, 20, 0, 6, 50, 200);
        $this->addTroopType("Spearman", 1, 50, 40, 10, 5, 100, 150);
        $this->addTroopType("Axemen", 1, 50, 50, 100, 4, 200, 100);
        $this->addTroopType("Light Cavalry", 1, 100, 100, 50, 10, 2000, 50);
    }

    public function addTroopType($troop_name, $level, $attack, $defense, $damage, $speed, $cost, $total_ini)
    {

        $unit = new UnitType();
        $unit->setName($troop_name);
        $unit->setLevel($level);
        $unit->setAttack($attack);
        $unit->setDefense($defense);
        $unit->setDamage($damage);
        $unit->setSpeed($speed);
        $unit->setCost($cost);
        $unit->setTotalInitial($total_ini);

        $this->em->persist($unit);
        $this->em->flush();

    }

    public function addBuildingType($building_name, $cost, $level, $defense, $capacity, $minimal_unit)
    {

        $building = new BuildingType();
        $building->setName($building_name);
        $building->setCost($cost);
        $building->setLevel($level);
        $building->setCapacity($capacity);
        $building->setDefense($defense);
        $building->setMinimalUnit($minimal_unit);

        $this->em->persist($building);
        $this->em->flush();

    }

    public function addKingdom($kingdom_name, $image, $color_class)
    {

        //Seleccionar el Team
        $kingdom = new Kingdom();

        $kingdom->setName($kingdom_name);
        $kingdom->setImage($image);
        $kingdom->setColor_class($color_class);

        $this->em->persist($kingdom);
        $this->em->flush();

    }

    public function addUser($name, $user_name, $password, $email, $role, $kingdom_name)
    {

        $kingdom = $this->em->getRepository(Kingdom::class)->findOneBy(['name' => $kingdom_name]);

        // Creando un usuario
        $user = new User();

        $user->setEmail($email);
        $user->setUsername($user_name);
        $user->setName($name);

        $encoded = $this->encoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $user->setGold(500000);

        $user->setRole($role);

        //Creando el Team del User
        $user->setKingdom($kingdom);

        //Guardarlo en BD

        $this->em->persist($user);
        $this->em->flush();

        //Comprobar si no existen mas usuarios en ese kingdom, entonces el usuario es el kingdom_boss

        $users_in_kingdom = $this->em->getRepository(User::class)->findBy(['kingdom' => $kingdom->getId()]);

        $total_users_en_kingdom = count($users_in_kingdom);
       // echo "\n";

       // echo "User: " . $user->getName() . " " . $total_users_en_kingdom . " " . $kingdom->getName();

        if ($total_users_en_kingdom <= 1) {
            $kingdom->setIdKingdomBoss($user->getId());
            $this->em->persist($kingdom);
            $this->em->flush();
        }


        $this->addUserInitialBuildings($user, $kingdom);
        $this->addUserInitialTroops($user, $kingdom);

    }

    public function addUserInitialTroops(User $user)
    {
        $this->CreateTroops($user);
    }

    public function addUserInitialBuildings(User $user, Kingdom $kingdom)
    {
      $this->createCasttle($user, $kingdom);
      $this->crearBarrack($user, $kingdom);
    }

   


    //----------------------------------------------------------------------------------------------------
    // Crea castillo en tabla buildings
    //----------------------------------------------------------------------------------------------------
    public function createCasttle(User $user, Kingdom $kingdom)
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
        $castillo = $this->em->getRepository(Building::class)->findOneBy(['kingdom' => $kingdom, 'buildingType' => $castle_type_lv1]);

        //busco si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
        if ($castillo == null) {
            $castillo = $this->em->getRepository(Building::class)->findOneBy(['kingdom' => $kingdom, 'buildingType' => $castle_type_lv2]);
        }

        //si no existe castillo lvl1 ni lv2 lo creo
        if ($castillo == null) {

            $castillo = new Building();
            $castillo->setUser($user);
            $castillo->setName2($castle_type_lv1->getName());
            $castillo->setBuildingType($castle_type_lv1);

            $castillo->setDefenseRemaining($castle_type_lv1->getDefense());
            $castillo->setKingdom($kingdom);

            $this->em->persist($castillo);
            $this->em->flush();

            $kingdom->setMainCastleId($castillo->getId());
            $kingdom->setIdKingdomBoss($user->getId());

            $this->em->persist($kingdom);

            $this->em->flush();



            //setear el boss del castillo
        }

    }

    public function crearBarrack(User $user, Kingdom $kingdom)
    {

        //Crear barracks
        $barrack_type = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'Barrack', 'level' => 1]);
        $barrack = new Building();

        $barrack->setUser($user);
        $barrack->setBuildingType($barrack_type);
        $barrack->setName2($barrack_type->getName());
        $barrack->setKingdom($kingdom);

        $barrack->setDefenseRemaining($barrack_type->getDefense());

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


    public function addConfig(){
               //------------------------------------------------------------------------
        //Configuracion global
        //------------------------------------------------------------------------
        $config = new Config();
        $config->setTesting(false);
        $config->setGoldIni(500000);
        $config->setTest_user('axl');
        $this->em->persist($config);
        $this->em->flush();
    }

}
