<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CreateInitialUserData
 *
 *
 */
class CreateInitialUserData
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function crear(User $user)
    {

        $this->createCasttle($user);

        return;

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

        //busco team del user ()
        $team = $this->em->getRepository(Team::class)->findOneBy(['user' => $user->getID()]);

        //busco el boss del team (es el duenno del castillo)
        $kingdom = $team->getKingdom();
        $id_user_boss = $kingdom->getIdKingdomBoss();

        //si no existe boss --> crear el castillo

        //busco el id de los tipos de castillo (TODO: crear un ciclo filtrar por name=castle)
        $castle_type_lv1 = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level' => 1]);
        $castle_type_lv2 = $this->em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level' => 2]);

        //busco si existe un castillo lv1 para el team de ese usuario
        $castillo = $this->em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType' => $castle_type_lv1]);

        //busco si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
        if ($castillo == null) {
            $castillo = $this->em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType' => $castle_type_lv2]);
        }

        //si no existe castillo lvl1 ni lv2 lo creo
        if ($castillo == null) {

            $castillo = new Building();
            $castillo->setUser($user);
            $castillo->setBuildingType($castle_type_lv1);

            $castillo->setDefenseRemaining($castle_type_lv1->getDefense());

            $this->em->persist($castillo);

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
