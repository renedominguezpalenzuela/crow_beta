<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\BuildingType;
use App\Entity\User;
use App\Service\GlobalConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//TODO: pasar como parametro el id del edificio
class APIUpgradeController extends AbstractController
{
    /**
     * @Route("/upgrade", name="upgrade")
     */
    public function index(Request $request, GlobalConfig $global_config)
    {
        $mensaje_error = "Not error found";
        $error = false;
        $resultado = '';

        $em = $this->getDoctrine()->getManager();
        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
            $user = $fake_user;

        } else {

            //--------------------------------------------------------------------------------------------------
            // Validando si usuario autenticado correctamente
            //--------------------------------------------------------------------------------------------------

            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {

                $respuesta = array(
                    'error' => true,
                    'message' => "User not authenticated",
                );

                return $this->json($respuesta, Response::HTTP_OK);

            }
            //usuario real si testing mode = false
            $user = $this->getUser();
        }

        //-------------------------------------------------------------------------------------------------
        //(2) Obteniendo los datos que vienen en la peticion
        //--------------------------------------------------------------------------------------------------
        $parametersAsArray = [];
        $content = $request->get("peticion");

        //variable que contendra el json como un arreglo
        $parametersAsArray = json_decode($content, true);

        //Datos del ataque: arreglo con tropas atacantes, id del edificio a atacar
        $building_id = $parametersAsArray["building_id"];

        //primero obtengo el level actual

        $building = $em->getRepository(Building::class)->findOneBy(['id' => $building_id]);

        $building_type_actual = $building->getBuildingType();
        $nombre_edificio = $building_type_actual->getName();

        //echo $nombre_edificio;

        $level_actual = $building_type_actual->getLevel();

        //luego busco el siguiente level
        $level_proximo = $level_actual + 1;
        //Compruebo que exista en la tabla BuildingType
        $building_type_next_level = $em->getRepository(BuildingType::class)->findOneBy(['name' => $nombre_edificio, 'level' => $level_proximo]);

        if ($building_type_next_level == null) {
            //Ya no existe ese nivel

            $respuesta = array(
                'error' => true,
                'message' => "Building can't be upgraded more",
            );

            return $this->json($respuesta, Response::HTTP_OK);

        }

        //Comprobar si existe oro suficiente

        $kingdom = $user->getKingdom();
        $oro_del_kingdom = $kingdom->getGold();
        $oro_necesario = $building_type_next_level->getCost();

     //  echo "Oro del kingdom ".$oro_del_kingdom;
     //  echo "Oro Necesario ".$oro_necesario;
        if ($oro_del_kingdom<$oro_necesario) {
            $respuesta = array(
                'error' => true,
                'message' => "Not enough gold to upgrade",
            );

            return $this->json($respuesta, Response::HTTP_OK);
        }

        //Continuamos:
        //Disminuir ORO
        $oro_final = $oro_del_kingdom -$oro_necesario;
        $kingdom->setGold($oro_final);
        $em->persist($kingdom);

        //Modificar building.building_type
        $building->setBuildingType($building_type_next_level);
        $building->setDefenseRemaining($building_type_next_level->getDefense());
        $em->persist($building);







        $em->flush();

        

//Objetivo:
        //- cambiar en building.building_type_id -- el tipo de edificio que se corresponde al next level
        //- Descontar el dinero del fondo comun del kingdom kingdom.gold (comprobar que alcanza)
        //- Actualizar la vida del edificio

//var_dump( $building_type_next_level);

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
        );

        return $this->json($respuesta, Response::HTTP_OK);

    }
}
