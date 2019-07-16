<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\BuildingType;
use App\Entity\Troop;
use App\Entity\TroopBuilding;
use App\Entity\User;
use App\Service\GlobalConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIListUserResourcesController
 * @package App\Controller
 * @Route("/api")
 */
class APIListUserResourcesController extends AbstractController
{

    //Parametros
    /**
     *  NO necesita parametros
     *   peticion_all = {}
     *   peticion_all.listar_kingdom_resources = 1;  -- > lista los edificios propios
     *   peticion_all.listar_kingdom_resources = 0;  -- > lista los edificios enemigos
     *   var datos_enviar = {
     *   peticion: JSON.stringify(peticion_all)
    }
     *  *///

    /**
     * @Route("/list_user_resources", name="list_user_resources")
     */
    public function list_user_resources(Request $request, GlobalConfig $global_config)
    {
        $mensaje_error = "Not error found";
        $error = false;

        if (!$global_config->isTestMode()) {

            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {

                $error = true;

                $respuesta = array(
                    'error' => false,
                    'message' => "User not authenticated",
                );

                return $this->json($respuesta);

            }
        }

        // $peticion_json = $request->get("peticion");

        //$peticion = json_decode($peticion_json);

        // $peticion->listar_kingdom_resources

        $arreglo_final = [
            'team' => '',
            'castle' => '',
            'buildings' => '',
            'troops' => '',
            'troops_location' => '',
            'resources' => '',
            'squads' => '',
        ];

        $em = $this->getDoctrine()->getManager();

        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
            $user = $fake_user;

        } else {
            //usuario real si testing mode = false
            $user = $this->getUser();
        }

        //--------------------------------------------------------------------------
        //(2) busco el castillo del usuario
        //--------------------------------------------------------------------------
        //2.1) busco el team del usuario

        //$team = $em->getRepository(Team::class)->findOneBy(['user' => $user->getID()]);
        //var_dump("User id " . $team->getID());

        //2.2) buscar el kingdom del team
        $kingdom = $user->getKingdom();

        $arreglo_final['team'] = array(
            'team_id' => $kingdom->getID(),
            'kingdom_name' => $kingdom->getName(),
        );

        //2.3) buscar el castle del team

        //busco el id de los tipos de castillo

        //busco si existe un castillo lv1 para el team de ese usuario
        $castle_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Castle', 'level' => 1]);
        $castillo = $em->getRepository(Building::class)->findOneBy(['kingdom' => $kingdom, 'buildingType' => $castle_type]);

        //si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
        if ($castillo == null) {
            $castle_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Castle', 'level' => 2]);
            $castillo = $em->getRepository(Building::class)->findOneBy(['kingdom' => $kingdom, 'buildingType' => $castle_type]);
        }

        $arreglo_final['castle'] = array(
            'castle_id' => $castillo->getID(), //Array del castillo en building
            'level' => $castle_type->getLevel(),
            'capacity' => $castle_type->getCapacity(),
            'defense_remaining' => $castillo->getDefenseRemaining(),
        );
        //----------------------------------------------------------------------------------------
        //(3) Buildings
        //----------------------------------------------------------------------------------------
        //busco todos los edificios del User excepto el castillo que ya lo tengo
        //TODO: Agregar todos los edificios del kingdom

        //$agregar = false;

        // if ($peticion->listar_kingdom_resources) {
        $buildings = $em->getRepository(Building::class)->findBy(['user' => $user]);
        // } else {
        //    $kingdom = $user->getKingdom()->getID();
        //    $buildings = $em->getRepository(Building::class)->BuscarEdificiosEnemigos($kingdom);
        // }

        $arreglo = array();

        //Agregando el castillo
        $arreglo[] = array(
            'building_id' => $castillo->getID(),
            'building_name' => $castle_type->getName(),
            'building_name2' => $castillo->getName2(),
            'capacity' => $castle_type->getCapacity(),
            'filled' => 0,
            'level' => $castle_type->getLevel(),
            'defense_remaining' => $castillo->getDefenseRemaining(),
            'kingdom' => $castillo->getKingdom()->getID(),
        );

        foreach ($buildings as $unbuilding) {

            //El castillo tambien se agrega en la lista lo que no se dibuja en el twig junto a los otros edificios
            //Cantidad de tropas en un edificio

            $building_type = $unbuilding->getBuildingType();

            if ($building_type->getName() != 'Castle') {

                $arreglo[] = array(
                    'building_id' => $unbuilding->getID(),
                    'building_name' => $building_type->getName(),
                    'building_name2' => $unbuilding->getName2(),
                    'capacity' => $building_type->getCapacity(),
                    'filled' => 0,
                    'level' => $building_type->getLevel(),
                    'defense_remaining' => $unbuilding->getDefenseRemaining(),
                    'kingdom' => $unbuilding->getKingdom()->getID(),
                );
            }

        }; //fin del loop

        //TODO: agregar los edificios de los otros miembros del team

        $arreglo_final['buildings'] = $arreglo;

        //------------------------------------------------------------------------------
        //(4) Troops
        //------------------------------------------------------------------------------
        //Busco todas las tropas del usuario
        $troops = $em->getRepository(Troop::class)->findBy(['user' => $user]);

        $arreglo = array();
        foreach ($troops as $untroop) {

            $troop_type = $untroop->getUnitType();

            $arreglo[] = array(
                'troop_id' => $untroop->getID(),
                'troop_name' => $troop_type->getName(),
                'level' => $untroop->getLevel(),
                'total' => $untroop->getTotal(),
                'attack' => $untroop->getAttack(),
                'defense' => $untroop->getDefense(),
                'damage' => $untroop->getDamage(),
                'speed' => $untroop->getSpeed(),
            );

        };

        $arreglo_final['troops'] = $arreglo;

        //------------------------------------------------------------------------------
        //(5) TroopsLocation
        //------------------------------------------------------------------------------
        //Busco todas las ubicaciones de las tropas del usuario
        $troops = $em->getRepository(Troop::class)->findBy(['user' => $user]);

        $arreglo = array();
        foreach ($troops as $untroop) {
            $troop_buildings = $em->getRepository(TroopBuilding::class)->findBy(['troops' => $untroop]);

            foreach ($troop_buildings as $untroop_building) {
                $arreglo[] = array(
                    'troop_id' => $untroop->getID(),
                    'troop_name' => $untroop->getUnitType()->getName(),
                    'building_id' => $untroop_building->getBuilding()->getID(),
                    'building_name' => $untroop_building->getBuilding()->getBuildingType()->getName(),
                    'total' => $untroop_building->getTotal(),
                );
            }

        }

        $arreglo_final['troops_location'] = $arreglo;

        $arreglo_final['resources'] = array(
            'gold' => $user->getGold(),
        );

        //Respuesta
        $respuesta = array(
            'datos' => $arreglo_final,
            'error' => $error,
            'message' => $mensaje_error,
        );

        //return $this->json(Response::HTTP_OK);

        return $this->json($respuesta, Response::HTTP_OK);
    }

    /**
     * @Route("/list_enemy_buildings", name="list_enemy_buildings")
     */
    public function list_enemy_buildings(Request $request, GlobalConfig $global_config)
    {

        $mensaje_error = "Not error found";
        $error = false;

        if (!$global_config->isTestMode()) {

            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {

                $error = true;

                $respuesta = array(
                    'error' => false,
                    'message' => "User not authenticated",
                );

                return $this->json($respuesta);

            }
        }

        $em = $this->getDoctrine()->getManager();
        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
            $user = $fake_user;

        } else {
            //usuario real si testing mode = false
            $user = $this->getUser();
        }

        $arreglo_final = [
            'buildings' => '',
            'castle_troops' => '',
        ];

        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
            $user = $fake_user;

        } else {
            //usuario real si testing mode = false
            $user = $this->getUser();
        }

        //--------------------------------------------------------------------------
        //(2) busco el castillo del usuario
        //--------------------------------------------------------------------------

        //2.1) buscar el kingdom del usuario

        $kingdom = $user->getKingdom();

        $buildings = $em->getRepository(Building::class)->BuscarEdificiosEnemigos($kingdom->getID());

        $arreglo_buildings = array();

        foreach ($buildings as $unbuilding) {

            $building_type = $unbuilding->getBuildingType();

            if ($building_type->getName() != 'Barrack') {

                //Buscar las tropas en este building
                $troop_buildings = $em->getRepository(TroopBuilding::class)->findBy(['building' => $unbuilding]);

                $arreglo_troops = array();

                foreach ($troop_buildings as $onetroop) {

                    $temp_troop = $em->getRepository(Troop::class)->find($onetroop->getTroops());

                    //Comprobar si ya tengo ese tipo de tropa registrado
                    //si si sumar, si no crear nueva tupla
                    $ya_registrada = false;
                    $indice = -1;
                    $i = -1;
                    $total = 0;
                    foreach ($arreglo_troops as $unatropas_registradas) {
                        $i = $i + 1;
                        if ($unatropas_registradas['troop_name'] == $temp_troop->getUnitType()->getName()) {
                            $indice = $i;
                            $ya_registrada = true;
                            $total = $unatropas_registradas['total'];
                            break;
                        }
                    }

                    if ($ya_registrada) {
                        $arreglo_troops[$indice] = array(
                            'troop_id' => $temp_troop->getID(),
                            'troop_name' => $temp_troop->getUnitType()->getName(),
                            'building_id' => $onetroop->getBuilding()->getID(),
                            'building_name' => $onetroop->getBuilding()->getBuildingType()->getName(),
                            'kingdom_name' => $onetroop->getBuilding()->getKingdom()->getName(),
                            'total' => $onetroop->getTotal() + $total,
                        );
                    } else {
                        $arreglo_troops[] = array(
                            'troop_id' => $temp_troop->getID(),
                            'troop_name' => $temp_troop->getUnitType()->getName(),
                            'building_id' => $onetroop->getBuilding()->getID(),
                            'building_name' => $onetroop->getBuilding()->getBuildingType()->getName(),
                            'kingdom_name' => $onetroop->getBuilding()->getKingdom()->getName(),
                            'total' => $onetroop->getTotal());

                    }

                }

                //if ($arreglo_troops!=null){
                $arreglo_buildings[] = array(
                    'building_id' => $unbuilding->getID(),
                    'building_name' => $building_type->getName(),
                    'building_name2' => $unbuilding->getName2(),
                    'capacity' => $building_type->getCapacity(),
                    'filled' => 0,
                    'level' => $building_type->getLevel(),
                    'defense_remaining' => $unbuilding->getDefenseRemaining(),
                    'kingdom' => $unbuilding->getKingdom()->getID(),
                    'kingdom_name' => $unbuilding->getKingdom()->getName(),
                    'color_class' => $unbuilding->getKingdom()->getColor_class(),
                    'troops_location' => $arreglo_troops,
                );
            }

        }; //fin del loop

        $arreglo_final['buildings'] = $arreglo_buildings;

        //Buscar las tropas en el castillo
        //busco si existe un castillo lv1 para el team de ese usuario
        $castle_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Castle', 'level' => 1]);

        $castillo = $em->getRepository(Building::class)->findOneBy(['kingdom' => $kingdom, 'buildingType' => $castle_type]);

        //si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
        if ($castillo == null) {
        $castle_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Castle', 'level' => 2]);
        $castillo = $em->getRepository(Building::class)->findOneBy(['kingdom' => $kingdom, 'buildingType' => $castle_type]);
        }

        //Busco todas las tropas ubicadas en el castillo

        $troop_buildings = $em->getRepository(TroopBuilding::class)->findBy(['building' => $castillo]);

        $arreglo_troops_on_castle = array();

        foreach ($troop_buildings as $onetroop) {

        $temp_troop = $em->getRepository(Troop::class)->find($onetroop->getTroops());

        $arreglo_troops_on_castle[] = array(
        'troop_id' => $temp_troop->getID(),
        'troop_name' => $temp_troop->getUnitType()->getName(),
        'building_id' => $onetroop->getBuilding()->getID(),
        'building_name' => $onetroop->getBuilding()->getBuildingType()->getName(),
        'kingdom_name' => $onetroop->getBuilding()->getKingdom()->getName(),
        'total' => $onetroop->getTotal(),
        );

        }


        $arreglo_final['castle_troops'] = $arreglo_troops_on_castle;
        
        //Respuesta
        $respuesta = array(
            'datos' => $arreglo_final,
            'error' => $error,
            'message' => $mensaje_error,
        );

        return $this->json($respuesta, Response::HTTP_OK);

    }
}
