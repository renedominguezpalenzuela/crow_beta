<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\BuildingType;
use App\Entity\Troop;
use App\Entity\TroopBuilding;
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
        $hired_troop_string_from_frontEnd = $parametersAsArray["id_tropas"];

        //1) Busco el id del tipo de tropa seleccionado en tabla: Unit_Type
        $UnitType_id = $this->getTroopTypeID_onTable_UnitType($hired_troop_string_from_frontEnd, $em);
        //var_dump($UnitType_id);

        //2) busco si el usuario tiene ese tipo de tropa en tabla: Troop
        //(obtengo el id de esa tropa para ese usuario)
        $user_id = $user->getID();
        $UserTroopID = $this->getUserTroopID($user_id, $UnitType_id, $em);
        //var_dump($UserTroopID);

        //3) busco el id de la barraca del usuario en tabla: Building
        $userBarrackID = $this->getUserBarrackID($user_id, $em);
        //var_dump($userBarrackID);

        $total_adicionar = $this->getTotalaAdicionar($hired_troop_string_from_frontEnd);
       // var_dump($total_adicionar);

        //4) Descontar el dinero
        $saldo_final = $this->descontarDinero($user, $UnitType_id, $total_adicionar, $em);

        if ($saldo_final < 0) {
            $error = true;
            $mensaje_error = "Not enough funds to hire units";
        }

        //5) Adicionar soldados a la barraca
        //busco si tiene el tipo de tropa ($UserTroopID) en la barraca ($userBarrackID)
        //si existe lo sumo, sino creo un nuevo articulo en TroopBuilding
        //adiciono el tipo de tropa en Troop
        //SOLO SI el dinero es suficiente
        if ($saldo_final >= 0) {
            $this->addTroops($total_adicionar, $user_id, $UserTroopID, $userBarrackID, $em);
            
        }

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
        );

     
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

        $id = $em->getRepository(UnitType::class)->findOneBy(['name' => $name_on_table])->getID();

        return $id;

    }

    //------------------------------------------------------------------------------------------
    //Obtiene el id de un tipo de tropa para un usuario
    //identifica de forma unica ese tipo de tropa de ese usuario en cualquier edificio
    //------------------------------------------------------------------------------------------
    public function getUserTroopID($user_id, $UnitType_id, $em)
    {

        $id = -1;

        $troop = $em->getRepository(Troop::class)->findOneBy(['unitType' => $UnitType_id, 'user' => $user_id]);

        if ($troop != null) {
            $id = $troop->getID();
        }

        return $id;

    }

    //-----------------------------------------------------------------------------------------
    // Busco la barraca del usuario
    //-----------------------------------------------------------------------------------------

    public function getUserBarrackID($user_id, $em)
    {

        $id = -1;

        //Busco el id del tipo de edificio barraca
        $barrack_type_id = -1;
        $barrack_type = $em->getRepository(BuildingType::class)->findOneBy(['name' => 'Barrack']);

        if ($barrack_type != null) {
            $barrack_type_id = $barrack_type->getId();
        }

        $userBarrack = $em->getRepository(Building::class)->findOneBy(['buildingType' => $barrack_type_id, 'user' => $user_id]);

        if ($userBarrack != null) {
            $id = $userBarrack->getID();
        }

        return $id;

    }

    //----------------------------------------------------------------------
    // 4) Adicionar soldados a la barraca
    //----------------------------------------------------------------------
    //busco si tiene el tipo de tropa ($UserTroopID) en la barraca ($userBarrackID) en tabla TroopBuilding
    //si existe lo sumo, sino creo un nuevo articulo en TroopBuilding
    //adiciono el tipo de tropa en Troop
    public function addTroops($total_a_adicionar, $user_id, $UserTroopID, $userBarrackID, $em)
    {

        //Tabla: troopBuilding;
        $troopBuilding = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $UserTroopID, 'building' => $userBarrackID]);
        $total_existente = 0;

        if ($troopBuilding != null) {
            $total_existente = $troopBuilding->getTotal();

        } else {
            $troopBuilding = new TroopBuilding();

        }

        $troopBuilding->setTotal($total_existente + $total_a_adicionar);
        $em->persist($troopBuilding);

        $total_existente = 0;
        //Tabla: Troop
        $troop = $em->getRepository(Troop::class)->findOneBy(['unitType' => $UserTroopID, 'user' => $user_id]);

        if ($troop != null) {

            $total_existente = $troop->getTotal();

            $troop->setTotal($total_existente + $total_a_adicionar);

            $em->persist($troop);
        }

        $em->flush();

    }

    public function getTotalaAdicionar($id_from_front_end)
    {

        $total_a_adicionar = 0;

        switch ($id_from_front_end) {
            case "hire-cavalry":{
                    $total_a_adicionar = 50;
                    break;
                }

            case "hire-axemen":{
                    $total_a_adicionar = 100;
                    break;
                }

            case "hire-spearmen":{
                    $total_a_adicionar = 100;
                    break;
                }

            case "hire-archer":{
                    $total_a_adicionar = 100;
                    break;
                }

        }

        return $total_a_adicionar;

    }

    //Descontar el dinero
    //si no alcanza devuelve -1,
    //se alcanza devuelve 0 o mayor que 0
    public function descontarDinero($user, $UnitType_id, $total_adicionar, $em)
    {

        $saldo_final = -1;

        //Buscar dinero actual del usuario

        $saldo_inicial = $user->getGold();



        $costo_una_Tropa = $em->getRepository(UnitType::class)->find($UnitType_id)->getCost();

       
        $costo_todas_tropas = $costo_una_Tropa * $total_adicionar;
        

        $saldo_final = $saldo_inicial - $costo_todas_tropas;

      

        if ($saldo_final >= 0) {
            $user->setGold($saldo_final);
            $em->persist($user);
            $em->flush();
        }

        return $saldo_final;

    }
}
