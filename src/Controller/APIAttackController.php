<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\Troop;
use App\Entity\TroopBuilding;
use App\Entity\User;
use App\Service\GlobalConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APICreateSquadController
 * @package App\Controller
 * @Route("/api")
 */
class APIAttackController extends AbstractController
{

    //Constantes resultados de attaque
    const VICTORY = 0;
    const DEFEAT = 1;
    const STALEMATE = 2;

    private $resultados_string = array("Victory", "Defeat", "Stalemate");

    private $session;

    //Inyectando el servicio session
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    //Extructura datos en la peticion
    //peticion: {"troops":[{"troops_id":1,"total":13},{"troops_id":2,"total":3}],"attacked_building":"3"}

    /**
     * @Route("/attack", name="attack", methods={"POST"})
     */

    public function ataque(Request $request, GlobalConfig $global_config)
    {
        $mensaje_error = "Not error found";
        $error = false;
        $resultado = '';

        //--------------------------------------------------------------------------------------------------
        // Validando si usuario autenticado correctamente
        //--------------------------------------------------------------------------------------------------

        /*  if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){

        $respuesta=array(
        'error'=>false,
        'message'=>"User not authenticated",
        );

        return $this->json($respuesta, Response::HTTP_OK);

        }*/

        //-------------------------------------------------------------------------------------------------
        //(1) Obteniendo los datos que vienen en la peticion
        //--------------------------------------------------------------------------------------------------

        $parametersAsArray = [];
        $content = $request->get("peticion");

        //variable que contendra el json como un arreglo

        $parametersAsArray = json_decode($content, true);

        $attacker_troops = $parametersAsArray["troops"];
        $attacked_building_id = $parametersAsArray["attacked_building"];

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

        //---------------------------------------------------------------------------------------------
        //Calculando resultado de la batalla
        //---------------------------------------------------------------------------------------------

        $resultado_batalla_attacker = mt_rand(0, 2);
        $resultado_batalla_defender = null;

        $texto_resultado_attacker = $this->resultados_string[$resultado_batalla_attacker];
        $texto_resultado_defender = '';

        switch ($resultado_batalla_attacker) {
            case self::VICTORY:
                $resultado_batalla_defender = self::DEFEAT;
                break;
            case self::DEFEAT:
                $resultado_batalla_defender = self::VICTORY;
                break;
            case self::STALEMATE:
                $resultado_batalla_defender = self::STALEMATE;
                break;
            default:
                $texto_resultado_defender = "not defined";

        }

        $texto_resultado_defender = $this->resultados_string[$resultado_batalla_defender];

        $datos_resultados_ataque = array();

        //-------------------------------------------------------------------------------------------
        // Edificio
        //-------------------------------------------------------------------------------------------
        $building = $em->getRepository(Building::class)->find($attacked_building_id);
        $building_name = $building->getName2() . ", " . $building->getKingdom()->getName();
        $building_initial_defense = $building->getDefenseRemaining();

        //Buscando nombre de tropas para mostrar en reporte final
        //Calculando fuerza total de ataque

        //----------------------------------------------------------------------------------------
        // Tropas atacantes
        //----------------------------------------------------------------------------------------
        $new_attacker_troops = array();

        $attacking_force_strenght = 0;

        foreach ($attacker_troops as $unatropa) {
            $id = $unatropa["troops_id"];
            $tropa = $em->getRepository(Troop::class)->find($id);
            $tipoUnidad = $tropa->getUnitType();
            $total = $unatropa["total"];

            $unatropa["name"] = $tipoUnidad->getName();

            $new_attacker_troops[] = $unatropa;

            $speed = $tipoUnidad->getSpeed();
            $attack = $tipoUnidad->getAttack();
            $defense = $tipoUnidad->getDefense();
            $damage = $tipoUnidad->getDamage();

            $attacking_force_strenght = $attacking_force_strenght + ($speed * $total * ($attack + $defense));

        }

        // var_dump($attacking_force_strenght);

        //----------------------------------------------------------------------------------------
        //  Tropas defensoras
        //----------------------------------------------------------------------------------------
        $defender_troops = [];
        $tropa_temporal = [];
        $troops_on_building = $em->getRepository(TroopBuilding::class)->findBy(['building' => $attacked_building_id]);

        $defending_force_strength = 0;

        foreach ($troops_on_building as $unatropa) {

            $tropa = $unatropa->getTroops();

            $tipoUnidad = $tropa->getUnitType();
            $total = $unatropa->getTotal();

            $tropa_temporal["name"] = $tipoUnidad->getName();
            $tropa_temporal["total"] = $total;
            $defender_troops[] = $tropa_temporal;

            $speed = $tipoUnidad->getSpeed();
            $attack = $tipoUnidad->getAttack();
            $defense = $tipoUnidad->getDefense();
            $damage = $tipoUnidad->getDamage();

            $defending_force_strength = $defending_force_strength + ($total * ($attack + $defense));
        }

        //TODO: sumarle la fuerza defensiva del edificio
        //$defending_force_strength =  $defending_force_strength + $defensa_edificio;

        $BF = 0;
        $LF = 0;
        $attacker_chance_of_victory = 0;
        $defender_chance_of_victory = 0;
        $staleChance = 0;

        //TODO: attacking_force_strenght>0, defending_force_strength>0

        //Determinando Porcientos
        if ($defending_force_strength > $attacking_force_strenght) { //Defensor mayor
            $BF = $defending_force_strength;
            $LF = $attacking_force_strenght;
            if ($LF <= 0) {$LF = 1;}

            $attacker_chance_of_victory = round(100 / (($BF / $LF) + 2), 2);

            $defender_chance_of_victory = round(($BF / $LF) * $attacker_chance_of_victory, 2);
            $staleChance = round(100 - $defender_chance_of_victory - $attacker_chance_of_victory, 2);

        } else { //Atacante mayor

            $BF = $attacking_force_strenght;
            $LF = $defending_force_strength;

            if ($LF <= 0) {$LF = 1;}

            $defender_chance_of_victory = round(100 / (($BF / $LF) + 2), 2);
            $attacker_chance_of_victory = round(($BF / $LF) * $defender_chance_of_victory, 2);
            $staleChance = round(100 - $attacker_chance_of_victory - $defender_chance_of_victory, 2);

        }

        //----------------------------------------------------------------------------------------
        // Eliminando tropas
        //----------------------------------------------------------------------------------------
        $lista_tropas_atacante = array();
        $lista_tropas_defensor = array();

        //------------------------------------------------------------------------------------------
        // lista_tropas_atacante
        //------------------------------------------------------------------------------------------
        $c = 0;
        foreach ($attacker_troops as $ungrupotropas) {

            $total = $ungrupotropas["total"];

            for ($i = 0; $i < $total; $i++) {
                $unaTropa["troops_id"] = $ungrupotropas["troops_id"];
                $lista_tropas_atacante[] = $unaTropa;
            }

        }

        //------------------------------------------------------------------------------------------
        // lista_tropas_defensores
        //------------------------------------------------------------------------------------------
        $c = 0;
        foreach ($troops_on_building as $unatropa) {

            $total = $unatropa->getTotal();

            for ($i = 0; $i < $total; $i++) {
                $unaTropa["troops_id"] = $unatropa->getTroops()->getId();
                $lista_tropas_defensor[] = $unaTropa;
            }

        }

        //------------------------------------------------------------------------------------------
        // encontrar el total de tropas a eliminar de los atacantes
        //------------------------------------------------------------------------------------------

        $porciento_tropas_eliminar_atacante = 0;
        $porciento_tropas_eliminar_defensor = 0;

        if ($resultado_batalla_attacker != self::STALEMATE) {
            $porciento_tropas_eliminar_atacante = $defender_chance_of_victory;
            $porciento_tropas_eliminar_defensor = $attacker_chance_of_victory;
        } else {
            $porciento_tropas_eliminar_atacante = $staleChance;
            $porciento_tropas_eliminar_defensor = $staleChance;

        }

        $lista_tropas_eliminadas_atacante = array();
        $lista_tropas_eliminadas_defensor = array();

        $lista_tropas_eliminadas_atacante = $this->eliminarTropas($lista_tropas_atacante, $porciento_tropas_eliminar_atacante);
        $lista_tropas_eliminadas_defensor = $this->eliminarTropas($lista_tropas_defensor, $porciento_tropas_eliminar_defensor);

        //Buscando el nombre de las tropas a eliminar para mostrar al usuario

       // $lista_tropas_eliminadas_atacante = $this->ponerNombreTropas($lista_tropas_eliminadas_atacante);
       // $lista_tropas_eliminadas_defensor = $this->ponerNombreTropas($lista_tropas_eliminadas_defensor);
        
 
        //-----------------------------------------------------------------------
        //  Modificar la BD
        //-----------------------------------------------------------------------

        //Modificar tropas del defensor en la BD
       // $this->modificarBD($lista_tropas_eliminadas_defensor, $building);


         //Modificar tropas del Atacante en la BD
        // $id_castillo_atacante = $user->getKingdom()->getMainCastleId();
        // $castillo_del_atacante = $em->getRepository(Building::class)->find($id_castillo_atacante);
        // $this->modificarBD($lista_tropas_eliminadas_atacante, $castillo_del_atacante );

        //----------------------------------------------------------------------------------------
        //  Enviando datos al cliente
        //----------------------------------------------------------------------------------------

        $datos_resultados_ataque = array(
            'building_initial_defense' => $building_initial_defense,
            'texto_resultado_defender' => $texto_resultado_defender,
            'texto_resultado_attacker' => $texto_resultado_attacker,
            'defending_force_strength' => $defending_force_strength,
            'attacking_force_strenght' => $attacking_force_strenght,
            'defender_chance_of_victory' => $defender_chance_of_victory,
            'attacker_chance_of_victory' => $attacker_chance_of_victory,
            'stale_chance' => $staleChance,
            'lista_tropas_eliminadas_atacante' => $lista_tropas_eliminadas_atacante,
            'lista_tropas_eliminadas_defensor' => $lista_tropas_eliminadas_defensor,
        );

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
            'result' => $texto_resultado_attacker,
            'troops_attacker' => $new_attacker_troops,
            'troops_defenders' => $defender_troops,
            'attacked_building' => $building_name,
            'resultados_ataque' => $datos_resultados_ataque,
        );

        if (!$error) {
            $em->flush();
        }

        return $this->json($respuesta, Response::HTTP_OK);

    }

    public function eliminarTropas($tropas_iniciales, $Porciento_a_Eliminar)
    {

        //Encontrando total a eliminar
        $total_tropas = count($tropas_iniciales);
        $total_a_eliminar = round(($Porciento_a_Eliminar * $total_tropas) / 100);

        //Reordenamiento aleatorio
        //shuffle($tropas_iniciales);

        //Escogiendo las N primeras al azar
        $lista_tropas_a_eliminar = array_slice($tropas_iniciales, 0, $total_a_eliminar);

        $lista_tropas_a_eliminar_condensada = array();

        //agrupar por id
        foreach ($lista_tropas_a_eliminar as $una_tropa) {

            $encontrado = false;
            $indice = 0;
            $total = 0;
            foreach ($lista_tropas_a_eliminar_condensada as $una_tropa_condensada) {

                if ($una_tropa_condensada["troops_id"] === $una_tropa["troops_id"]) {

                    // $una_tropa_condensada["total"] = $una_tropa_condensada["total"] + 1;
                    // $lista_tropas_a_eliminar_condensada["total"]= $lista_tropas_a_eliminar_condensada["total"]+1;
                    //var_dump($una_tropa_condensada);
                    $total = $una_tropa_condensada["total"] + 1;
                    $encontrado = true;
                    break;
                }
                $indice = $indice + 1;
            }

            if ($encontrado == false) {
                $una_tropa["total"] = 1;
                $lista_tropas_a_eliminar_condensada[] = $una_tropa;
            } else {
                $lista_tropas_a_eliminar_condensada[$indice]["total"] = $total;
            }

        }

        //devolver arreglo para mostrar al cliente
        return $lista_tropas_a_eliminar_condensada;
    }

    public function modificarBD($tropas_a_eliminar, $edificio)
    {

        $em = $this->getDoctrine()->getManager();

        foreach ($tropas_a_eliminar as $una_tropa) {

           // var_dump($una_tropa);

            //Total a eliminar
            $total_eliminar = $una_tropa["total"];

            $troop_id = $una_tropa["troops_id"];

            //Busco la tropa en el edificio
            $tropa_edificio = $em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $edificio]);

            //busco la tropa en Troops
            $tropa_Troops = $em->getRepository(Troop::class)->find($troop_id);

            //-------------------------------------------
            // Total en el edificio
            //-------------------------------------------
            $total_edificio = $tropa_edificio->getTotal();

            //Total final en edificio
            $total_final_edificio = $total_edificio - $total_eliminar;

            //-------------------------------------------
            // Total en Troops
            //-------------------------------------------
            $total_Troops = $tropa_Troops->getTotal();

            //total final en Troops
            $total_final_Troops = $total_Troops - $total_eliminar;

            //Modificar BD

            if ($total_final_edificio <= 0) {
                $total_final_edificio = 0;
                $em->remove($tropa_edificio);
                $em->flush();
            } else {
                $tropa_edificio->setTotal($total_final_edificio);
                $em->persist($tropa_edificio);
            }

            if ($total_final_Troops <= 0) {
                $total_final_Troops = 0;
                $em->remove($tropa_Troops);
                $em->flush();
            } else {
                $tropa_Troops->setTotal($total_final_edificio);
                $em->persist($tropa_Troops);
            }


        }

        $em->flush();
      

    }


    public function ponerNombreTropas($lista_tropas) {
        $em = $this->getDoctrine()->getManager();

        $c = 0;
        foreach ($lista_tropas as $una_tropa) {

            $troop_id = $una_tropa["troops_id"];
            $tropa_Troops = $em->getRepository(Troop::class)->find($troop_id);
             
            $lista_tropas[$c]["name"] =  $tropa_Troops->getUnitType()->getName();
            $c=$c+1;

        }

    }

}
