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

        $arreglo_final = [
            'team' => '',
            'castle' => '',
            'buildings' => '',
            'troops' => '',
            'troops_location' => '',
            'resources' => '',
        ];

        $em = $this->getDoctrine()->getManager();

        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $em->getRepository(User::class)->findOneBy(['name' => 'axl']);
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
        $castle_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level' => 1]);
        $castillo = $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType' => $castle_type]);

        //si no existe un castillo lv1 para el team de ese usuario, lo busco para el level 2
        if ($castillo == null) {
            $castle_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'castle', 'level' => 2]);
            $castillo = $em->getRepository(Building::class)->findOneBy(['user' => $user, 'buildingType' => $castle_type]);
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
        //busco todos los edificios del team excepto el castillo que ya lo tengo
        $buildings = $em->getRepository(Building::class)->findBy(['user' => $user]);

        $arreglo = array();
        foreach ($buildings as $unbuilding) {

            $building_type = $unbuilding->getBuildingType();

            //if ($building_type->getName() != 'Castle') {
            //El castillo tambien se agrega en la lista lo que no se dibuja en el twig junto a los otros edificios
            //Cantidad de tropas en un edificio

            $arreglo[] = array(
                'building_id' => $unbuilding->getID(),
                'building_name' => $building_type->getName(),
                'capacity' => $building_type->getCapacity(),
                'filled' => 0,
                'level' => $building_type->getLevel(),
                'defense_remaining' => $unbuilding->getDefenseRemaining(),
            );

        };

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

        return $this->json($respuesta, Response::HTTP_OK);
    }
}
