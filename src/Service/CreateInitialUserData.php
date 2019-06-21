<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Team;
use App\Entity\BuildingType;
use App\Entity\Building;

/**
 * Class CreateInitialUserData
 *
 *
 */

 //Ultima version 21-06-2019  3:48 pm
class CreateInitialUserData
{

    private $em;
    private $team;
    private $kingdom;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function crear(User $user)
    {
        //obtengo datos del team y el kingdom del usuario  
        $this->getKingdomData($user);

        //Creando el castillo
        $this->createCasttle($user);

        return;

    }


    public function getKingdomData(User $user){
        //1)busco kingdom del user 
        //el kingdom se escoge al crear el usuario
        $this->team = $this->em->getRepository(Team::class)->findOneBy(['user' => $user->getID()]);
        $this->kingdom = $this->team->getKingdom();

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
      //  var_dump($castle_type_lv1->getDefense());



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
            $castillo->setBuildingType($castle_type_lv1);

            $castillo->setDefenseRemaining($castle_type_lv1->getDefense());
            $castillo->setKingdom($this->kingdom);

            $this->em->persist($castillo);

            $this->em->flush();

            //setear el boss del castillo
        }

    }

}

/*
//Asignar al primer kingdom
$kingdom = $em->getRepository(Kingdom::class)->findOneBy(['name' => 'Test Kingdom']);

//verify if kingdom has leader and id_player_boss
// if ($kingdom->getIdKingdomBoss() == 0) {
$kingdom->setIdKingdomBoss($user->getId());
$em->persist($kingdom);
//}

//Creando el Team
$team = new Team();
$team->setKingdom($kingdom);
$team->setUser($user);
$team->setGold(500000);
$em->persist($team);

$em->flush();

//Guardarlo en BD

$em->persist($user);
$em->flush();

//Crear el resto de los datos del usuario
$this->CreateFakeBuildings($user);

//Crear tropas ubiacarlas inicialmente en la barraca
$this->CreateFakeTroops($user);

//Respuesta
$respuesta = array(
'error' => $error,
'message' => $mensaje_error,
);

//$this->borrarDatosPrueba();
return $this->json($respuesta);

 */
