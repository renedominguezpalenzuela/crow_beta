<?php

namespace App\Controller;

use App\Entity\Troop;
use App\Entity\UnitType;
use App\Entity\User;
use App\Service\GlobalConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
1) Compruebo que el dinero alcanza
2) Busco si ya tiene ese tipo de tropa Troops

2.1) si si --
2.1.1) obtengo el id de la tropa
2.1.2) Busco si tiene ese id de tropa en la barraca del jugador
2.1.2.1) Si si
- incremento el total en TroopBuilding
2.1.2.2) Si no
- Creo en la barraca la tropa
2.2) si no
2.2.1) Creo la tropa en Troops
Creo la tropa en la barraca

 */

class StoreController extends AbstractController
{

    //private $em = null;

    public function __construct()
    {
       // $this->em = $this->getDoctrine()->getManager();
       // $em = $this->getDoctrine()->getManager();
        //La primera tupla es la de config
        //$this->config = $this->em->getRepository(Config::class)->findAll()[0];
    }



    //Peticion recibida: {peticion={"id_tropas":"hire-spearmen"}}
    //TODO: agregar cantidad de tropas
    /**
     * @Route("/hire_troops", name="hire_troops", methods={"POST"})
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
        $hired_troop = $parametersAsArray["id_tropas"];

        $UnitType_id = $this->getTroopTypeID_onTable_UnitType($hired_troop, $em)->getID();

  

        //var_dump($UnitType_id);

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
            'UnitTypeID' =>$UnitType_id,

        );

        /* if (!$error) {
        $this->em->flush();
        }*/

        return $this->json($respuesta, Response::HTTP_OK);

    }

    //TODO: hacer dinamico la cantidad de tropas a comprar

    //------------------------------------------------------------------------------
    // Devuelve el ID del Tipo de Tropa en tabla Troops
    //------------------------------------------------------------------------------
    //recibe como parametro el id dado en el front end
    //  PArametros:
    //  ID_FronEnd        Name on Troop Table
    //  hire-cavalry          Light Cavalry
    //  hire-axemen           Axemen
    //  hire-spearmen         Spearman
    //  hire-archer           Archers

    

    public function getTroopTypeID_onTable_UnitType($id_from_front_end, $em)
    {

        $id = 0;

        $name_on_table = '';
        switch ($id_from_front_end) {
            case "hire-cavalry":{
                    $name_on_table = "Light Cavalry";
                    break;
                }

            case "hire-axemen":{
                    $name_on_table = "Axemen";
                    break;
                }

            case "hire-spearmen":{
                    $name_on_table = "Spearman";
                    break;
                }

            case "hire-archer":{
                    $name_on_table = "Archers";
                    break;
                }

        }

  
        $id = $em->getRepository(UnitType::class)->findOneBy(['name' => $name_on_table]);

        return $id;

    }

}
