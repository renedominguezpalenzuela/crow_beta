<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Battle;
use App\Service\GlobalConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APICreateSquadController
 * @package App\Controller
 * @Route("/api")
 */
class APIAttackController extends AbstractController
{

    // private $session;
    private $em;
    private $battle;

    //Constantes resultados de attaque
    /* const VICTORY = 0;
    const DEFEAT = 1;
    const STALEMATE = 2;

    private $resultados_string = array("Victory", "Defeat", "Stalemate");*/

    //Variables de la Batalla Contendran uno de los valores VICTORY, DEFEAT, STALEMAN
    private $resultado_batalla_attacker;
    private $resultado_batalla_defender;

    //Contiene el valor de la fuerza de ataque del atacante
    private $attacking_force_strenght;
    private $defending_force_strenght;

    //Calculo de bajas
    private $attacker_chance_of_victory = 0;
    private $defender_chance_of_victory = 0;
    private $staleChance = 0;

    //Edificio fuerza de defensa inicial
    private $building_initial_defense = 0;
    private $building_final_defense = 0;
    private $attacker_building_damage_strength = 0;

    //Inyectando el servicio session
    public function __construct( /*SessionInterface $session,*/EntityManagerInterface $entityManager, Battle $battle)
    {
        ///$this->session = $session;
        $this->em = $entityManager;
        $this->battle = $battle;
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

        //--------------------------------------------------------------------------
        //(1) Obtengo user() de la peticion
        //--------------------------------------------------------------------------
        if ($global_config->isTestMode()) {
            //Fake user si testing mode
            $fake_user = $this->em->getRepository(User::class)->findOneBy(['name' => $global_config->getTest_user()]);
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

        $id_attacker_castle = $user->getKingdom()->getMainCastleId();

       

        //-------------------------------------------------------------------------------------------------
        //(2) Obteniendo los datos que vienen en la peticion
        //--------------------------------------------------------------------------------------------------
        $parametersAsArray = [];
        $content = $request->get("peticion");

        //variable que contendra el json como un arreglo
        $parametersAsArray = json_decode($content, true);

        //Datos del ataque: arreglo con tropas atacantes, id del edificio a atacar
        $attacker_troops = $parametersAsArray["troops"];

        $attacked_building_id = $parametersAsArray["attacked_building"];
        $this->datos_resultados_ataque = array();

        //----------------------------------------------------------------------------------------
        // Tropas atacantes, calculando fuerza total
        //----------------------------------------------------------------------------------------

        $this->attacking_force_strenght = $this->battle->getAttackerForceStrength($attacker_troops, $this->attacker_building_damage_strength);

        //Si tropas atacantes <=0 no puede realizarse el ataque
        if ($this->attacking_force_strenght <= 0) {

            $respuesta = array(
                'error' => true,
                'message' => "Attacker Troops < 0",
            );

            return $this->json($respuesta, Response::HTTP_OK);
        }

        //--------------------------------------------------------------------------------------------
        // Tropas defensoras, calculando fuerza total
        //--------------------------------------------------------------------------------------------
        $building_name = '';
       
        $defender_troops = array();
        $this->defending_force_strenght = $this->battle->getDefenderForceStrength($attacked_building_id, $building_name, $defender_troops, $this->building_initial_defense);
     
        //--------------------------------------------------------------------------------------------
        // Calculo de Porcientos en funcion de las tropas de cada bando
        //--------------------------------------------------------------------------------------------
        $porciento_tropas_eliminar_atacante = 0;
        $porciento_tropas_eliminar_defensor = 0;
        $danos_a_edificios = 0; //Valor numerico de danos a edificios

        $this->battle->getPorcientos(
            $this->attacking_force_strenght,
            $this->defending_force_strenght,
            $this->attacker_chance_of_victory,
            $this->defender_chance_of_victory,
            $this->staleChance
        );

        // var_dump("Defensas ".$this->building_initial_defense);
        //---------------------------------------------------------------------------------------------
        //Calculando resultado de la batalla en funcion de los porcientos
        //---------------------------------------------------------------------------------------------

        //Valores numericos
        $this->resultado_batalla_attacker = $this->battle->getRamdomBattleResultforAttacker(
            $this->attacking_force_strenght, 
            $this->defending_force_strenght,
            $this->attacker_chance_of_victory,
            $this->defender_chance_of_victory,
            $this->staleChance
        );
        
        $this->resultado_batalla_defender = $this->battle->getOtherSideResult($this->resultado_batalla_attacker);

        if ($this->resultado_batalla_attacker == Battle::UNDEFINED) {

            $respuesta = array(
                'error' => true,
                'message' => "Battle result undefined",
            );

            return $this->json($respuesta, Response::HTTP_OK);
        }

        //Valores de Texto
        $this->texto_resultado_attacker = $this->battle->getResultadoString($this->resultado_batalla_attacker);
        $this->texto_resultado_defender = $this->battle->getResultadoString($this->resultado_batalla_defender);

        //---------------------------------------------------------------------------------------------
        //Calculando Danos a edificios
        //---------------------------------------------------------------------------------------------

        $danos_a_edificios = $this->battle->getDannos_a_edificios($this->resultado_batalla_attacker, $this->attacker_building_damage_strength);

        //Calculo del damage al edificio
        $this->building_final_defense = $this->building_initial_defense - $danos_a_edificios;
        if ($this->building_final_defense < 0) {
            $this->building_final_defense = 0;
        }

        //---------------------------------------------------------------------------------------------
        //Calculando Tropas a eliminar
        //---------------------------------------------------------------------------------------------

        $this->battle->getPorcientoTropasaEliminar($this->attacker_chance_of_victory,
            $this->defender_chance_of_victory,
            $this->staleChance,
            $this->resultado_batalla_attacker,
            $porciento_tropas_eliminar_atacante,
            $porciento_tropas_eliminar_defensor);

        //   var_dump("Attacker Strength ".$this->attacking_force_strenght." Defender Strength ". $this->defending_force_strenght);
        //   var_dump("Attacker Chance Victory ".$this->attacker_chance_of_victory." Defender Chance Victory ".$this->defender_chance_of_victory." Stalemate ".$this->staleChance);

        //--------------------------------------------------------------------------------------------
        // Buscando Tropas a eliminar
        //--------------------------------------------------------------------------------------------
        $attacker_troops_a_eliminar = array();
        $defender_troops_a_eliminar = array();

        // var_dump("Atacante " . $this->texto_resultado_attacker . " Tropas Perdidas " . round($porciento_tropas_eliminar_atacante) . " %");
        // var_dump("Defender " .  $this->texto_resultado_defender . " Tropas Perdidas " . round($porciento_tropas_eliminar_defensor) . " %");

        //var_dump("Attacker ");
        // var_dump($attacker_troops);
        $this->battle->getListaTropasAeliminar($attacker_troops, $porciento_tropas_eliminar_atacante, $attacker_troops_a_eliminar, $total_a_eliminar);
        // var_dump($attacker_troops_a_eliminar);

        //var_dump("Defender ");
        //var_dump($defender_troops);
        $this->battle->getListaTropasAeliminar($defender_troops, $porciento_tropas_eliminar_defensor, $defender_troops_a_eliminar, $total_a_eliminar);
        //var_dump($defender_troops_a_eliminar);

        $attacker_lista_condensada_a_eliminar = array();
        $this->battle->getListaCondensada($attacker_troops_a_eliminar, $attacker_lista_condensada_a_eliminar);

        $defender_lista_condensada_a_eliminar = array();
        $this->battle->getListaCondensada($defender_troops_a_eliminar, $defender_lista_condensada_a_eliminar);
        //var_dump( $defender_lista_condensada_a_eliminar);

        //--------------------------------------------------------------------------------------------
        // Eliminando tropas, Actualizando BD
        //--------------------------------------------------------------------------------------------
       // $this->battle->eliminarTroopsFromDB($attacker_lista_condensada_a_eliminar, $id_attacker_castle );
        $this->battle->eliminarTroopsFromDB($defender_lista_condensada_a_eliminar, $attacked_building_id);

        


        //----------------------------------------------------------------------------------------
        //  TODO: Captura de edificios
        //----------------------------------------------------------------------------------------
        //Determinar si no quedan tropas defensoras
        //--seleccionar y contar el total de tropas en el edificio en troop_building
        //Cambiar en building, el building id



        //----------------------------------------------------------------------------------------
        //  TODO: Damage a edificios
        //----------------------------------------------------------------------------------------

       

        //----------------------------------------------------------------------------------------
        //  TODO: Puntos
        //----------------------------------------------------------------------------------------


        //----------------------------------------------------------------------------------------
        //  Enviando datos al cliente
        //----------------------------------------------------------------------------------------

        $datos_resultados_ataque = array(
            'building_initial_defense' => $this->building_initial_defense,
            'building_final_defense' => $this->building_final_defense,
            'attacker_building_damage_strength' => $this->attacker_building_damage_strength,
            'building_damage' => $danos_a_edificios,
            'texto_resultado_attacker' => $this->texto_resultado_attacker,
            'texto_resultado_defender' => $this->texto_resultado_defender,
            'attacking_force_strenght' => $this->attacking_force_strenght,
            'defending_force_strength' => $this->defending_force_strenght,
            'attacker_chance_of_victory' => $this->attacker_chance_of_victory,
            'defender_chance_of_victory' => $this->defender_chance_of_victory,
            'stale_chance' => $this->staleChance,
            'bajas_atacante' => $attacker_lista_condensada_a_eliminar,
            'bajas_defensor' => $defender_lista_condensada_a_eliminar,
        );

        $respuesta = array(
            'error' => $error,
            'message' => $mensaje_error,
            'result' => $this->resultado_batalla_attacker,
            'troops_attacker' => $attacker_troops,
            'troops_defenders' => $defender_troops,
            'attacked_building' => $building_name,
            'resultados_ataque' => $datos_resultados_ataque,
        );

        if (!$error) {
            // $this->em->flush();
        }

        return $this->json($respuesta, Response::HTTP_OK);

    }

   

}
