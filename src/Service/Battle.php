<?php

namespace App\Service;

use App\Entity\Building;
use App\Entity\Config;
use App\Entity\Troop;
use App\Entity\TroopBuilding;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Battle
 *
 *
 */

class Battle
{
    private $em;
    private $config;

    //-------------------------------------------------------------------------------------------------
    //Constantes numericas resultados de attaque
    //-------------------------------------------------------------------------------------------------
    const VICTORY = 0;
    const DEFEAT = 1;
    const STALEMATE = 2;
    const UNDEFINED = -1;

    //-------------------------------------------------------------------------------------------------
    // Constantes de texto resultados del ataque
    //-------------------------------------------------------------------------------------------------
    private $resultados_string = array("Victory", "Defeat", "Stalemate");

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        //La primera tupla es la de config
        $this->config = $this->em->getRepository(Config::class)->findAll()[0];
    }

    //-------------------------------------------------------------------------------------------------
    // Devuelve cadena de texto que representa un estado de resultados del ataque
    //-------------------------------------------------------------------------------------------------
    public function getResultadoString($resultado_batalla_numerico)
    {
        return $this->resultados_string[$resultado_batalla_numerico];
    }

    //-------------------------------------------------------------------------------------------------
    // Devuelve un numero al azar del 0 al 2 que simboliza uno de los resultados del ataque
    //-------------------------------------------------------------------------------------------------
    public function getRamdomBattleResultforAttacker($attacking_force_strenght, $defending_force_strenght)
    {
        $resultado = Battle::UNDEFINED;

        if ($attacking_force_strenght > 0 && $defending_force_strenght > 0) {
            $resultado = mt_rand(0, 2);

        } else {

            if ($attacking_force_strenght <= 0 && $defending_force_strenght <= 0) {
                $resultado = Battle::UNDEFINED;

            } else {

                if ($defending_force_strenght <= 0) {
                    $resultado = Battle::VICTORY;

                }

                if ($attacking_force_strenght <= 0) {
                    $resultado = Battle::DEFEAT;

                }
            }

        }

        return $resultado;
    }

    //-------------------------------------------------------------------------------------------------
    // Dado el resultado del ataque de un bando devuelve el resultado del ataque del otro bando
    //-------------------------------------------------------------------------------------------------
    public function getOtherSideResult($Unbando)
    {
        $OtroBando = self::UNDEFINED;

        switch ($Unbando) {
            case self::VICTORY:
                $OtroBando = self::DEFEAT;
                break;
            case self::DEFEAT:
                $OtroBando = self::VICTORY;
                break;
            case self::STALEMATE:
                $OtroBando = self::STALEMATE;
                break;
            default:
                $OtroBando = self::UNDEFINED;
        }

        return $OtroBando;

    }

    //-----------------------------------------------------------------------------------------------
    // Calcula el poder de ataque de una tropa
    //-----------------------------------------------------------------------------------------------
    //Parametros
    // $attacker_troops = array()
    //
    /*
    array (size=2)
    0 =>
    array (size=2)
    'troops_id' => int 1
    'total' => int 13
    1 =>
    array (size=2)
    'troops_id' => int 2
    'total' => int 3

    Total calculado = 5250
     */
    //            a   de   da   s    Total
    // Archers : 30 : 20 :  0 : 6      13
    //Spearman : 50 : 40 : 10 : 5       3

    /*
    6 * 13 * (30 + 20)    --
    5 *  3 * (50 + 40)
     */

    public function getAttackerForceStrength(&$attacker_troops, &$attacker_building_damage_strength)
    {

        $attacking_force_strenght = 0;
        $new_attacker_troops = array();
        $attacker_building_damage_strength = 0;

        foreach ($attacker_troops as $unatropa) {
            $id = $unatropa["troops_id"];
            $tropa = $this->em->getRepository(Troop::class)->find($id);
            $tipoUnidad = $tropa->getUnitType();
            $total = $unatropa["total"];

            $unatropa["name"] = $tipoUnidad->getName();
            $unatropa["user"] = $tropa->getUser()->getName();

            $new_attacker_troops[] = $unatropa;

            $attack = $tipoUnidad->getAttack();
            $defense = $tipoUnidad->getDefense();
            $damage = $tipoUnidad->getDamage();
            $speed = $tipoUnidad->getSpeed();

            $attacker_building_damage_strength = $attacker_building_damage_strength + $damage *  $total;

            //var_dump($unatropa["name"] ." : ".$attack." : ".$defense." : ". $damage." : ".$speed );

            $attacking_force_strenght = $attacking_force_strenght + ($speed * $total * ($attack + $defense));
        }

        $attacker_troops = $new_attacker_troops;
        return $attacking_force_strenght;

    }

    public function getDefenderForceStrength($attacked_building_id, &$building_name, &$defender_troops, &$building_initial_defense)
    {

        //-------------------------------------------------------------------------------------------
        // Edificio, obteniendo el edificio atacado asi como sus datos nombre, defensa
        //-------------------------------------------------------------------------------------------
        $building = $this->em->getRepository(Building::class)->find($attacked_building_id);
        $building_name = $building->getName2() . ", " . $building->getKingdom()->getName();
        $building_initial_defense = $building->getDefenseRemaining();

       

        //----------------------------------------------------------------------------------------
        //  Tropas defensoras
        //----------------------------------------------------------------------------------------

        $defender_troops = array();

        //busco todas las tropas en el edificio (de todos los usuarios)
        $troops_on_building = $this->em->getRepository(TroopBuilding::class)->findBy(['building' => $attacked_building_id]);

        $defending_force_strength = 0;

        foreach ($troops_on_building as $unatropa) {

            $tropa_temporal = array();
            $tropa = $unatropa->getTroops();

            $tipoUnidad = $tropa->getUnitType();
            $total = $unatropa->getTotal();
            $tropa_temporal["troops_id"] = $tropa->getId();
            $tropa_temporal["name"] = $tipoUnidad->getName();
            $tropa_temporal["total"] = $total;
            $tropa_temporal["user"] = $tropa->getUser()->getName();

            $defender_troops[] = $tropa_temporal;

            $speed = $tipoUnidad->getSpeed();
            $attack = $tipoUnidad->getAttack();
            $defense = $tipoUnidad->getDefense();
            $damage = $tipoUnidad->getDamage();

            $defending_force_strength = $defending_force_strength + ($total * ($attack + $defense));
        }

        //Solo si existen tropas en el edificio se puede sumar las defensas del edificio,
        //sino no el edificio tiene 0 defensas
        if ($defending_force_strength > 0) {
            $defending_force_strength = $defending_force_strength + $building_initial_defense;
        }

        return $defending_force_strength;

    }

    //Casos de uso

    //1) DEF_F > ATTACK_F
    //2) DEF_F < ATTACK_F
    //3) DEF_F  = ATTACK_F
    //4) DEF_F = 0 and ATTACK_F = 0
    //5) DEF_F = 0 and ATTACK_F <> 0
    //6) DEF_F <> 0 and ATTACK_F = 0

    
    public function getPorcientos($attacking_force, $defending_force, &$attacker_chance_of_victory, &$defender_chance_of_victory, &$staleChance,
        $resultado_batalla_attacker, &$porciento_tropas_eliminar_atacante, &$porciento_tropas_eliminar_defensor,
         $attacker_building_damage_strength,   &$danos_a_edificios) {

        $BF = 0; //Biggest Force
        $LF = 0; //Lowest Force

        $LFCV = 0; //Lesser Force Chance of Victory
        $BFCV = 0; //Biggest Force Chance of Victory
        $staleChance = 0; //StaleMate Chance of Victory

        if ($defending_force > $attacking_force) {
            $BF = $defending_force;
            $LF = $attacking_force;

        } else {
            $BF = $attacking_force;
            $LF = $defending_force;

        }

        if ($BF > 0 && $LF > 0) {
            $LFCV = 100 / (($BF / $LF) + 2);
            $BFCV = ($BF / $LF) * $LFCV;

        } else {

            if ($LF <= 0) {
                $LFCV = 0;
                $BFCV = 100;

            }

            if ($BF <= 0) {
                $BFCV = 0;
                $LFCV = 100;

            }

        }

        //si BFCV<0 o LFCV <0 nunca sera $staleChance
        if ($BF <= 0 && $LF <= 0) {
            $BFCV = 0;
            $LFCV = 0;
            $staleChance = 0;
        } else {
            $staleChance = 100 - $BFCV - $LFCV;
        }

        $staleChance = round($staleChance, 2);
        $BFCV = round($BFCV, 2);
        $LFCV = round($LFCV, 2);

        //&$defender_chance_of_victor, &$attacker_chance_of_victory, &$staleChance

        if ($defending_force > $attacking_force) {

            $defender_chance_of_victory = $BFCV;
            $attacker_chance_of_victory = $LFCV;

        } else {
            $defender_chance_of_victory = $LFCV;
            $attacker_chance_of_victory = $BFCV;
        }

        //resultados  = round(X,2)
        //TODO: attacking_force_strenght>0, defending_force_strength>0

        if ($resultado_batalla_attacker != Battle::STALEMATE) {

            $porciento_tropas_eliminar_atacante = $defender_chance_of_victory;
            $porciento_tropas_eliminar_defensor = $attacker_chance_of_victory;

        } else {
            $porciento_tropas_eliminar_atacante = $staleChance;
            $porciento_tropas_eliminar_defensor = $staleChance;
        }



        //$attacker_building_damage_strength
        //$danos_a_edificios
        $porciento_a_aplicar = 0;

        //$porcientos_danos_a_edificios
        switch ($resultado_batalla_attacker) {
            case self::VICTORY:
                $porciento_a_aplicar = 100;
                break;
            case self::DEFEAT:
                $porciento_a_aplicar = 25;
                break;
            case self::STALEMATE:
                $porciento_a_aplicar = 50;
                break;
            default:
                $porciento_a_aplicar = 0;
        }

       // var_dump("Porciento ".$porciento_a_aplicar);
       // var_dump("Fuerza ataque a edificios ".$attacker_building_damage_strength);

        $danos_a_edificios = round(($porciento_a_aplicar * $attacker_building_damage_strength) / 100);

        //var_dump("Danno final ".$danos_a_edificios);



    }

    //Crea arreglo uniforme para eliminar tropas
    public function eliminarTropasAtacantes($attacker_troops, $porciento_a_eliminar)
    {

        $lista_tropas_atacante = array();

        //------------------------------------------------------------------------------------------
        // lista_tropas_atacante -- Todas las tropas
        //------------------------------------------------------------------------------------------
        $c = 0;
        foreach ($attacker_troops as $ungrupotropas) {

            $total = $ungrupotropas["total"];

            for ($i = 0; $i < $total; $i++) {
                $unaTropa["troops_id"] = $ungrupotropas["troops_id"];
                $lista_tropas_atacante[] = $unaTropa;
            }

        }

        $this->eliminarTropas($lista_tropas_atacante, $porciento_a_eliminar);

    }

    //Crea arreglo uniforme para eliminar tropas
    public function eliminarTropasDefensores($troops_on_building, $porciento_a_eliminar)
    {
        $lista_tropas_defensor = array();

        //------------------------------------------------------------------------------------------
        // lista_tropas_defensores -- Todas las tropas
        //------------------------------------------------------------------------------------------
        $c = 0;
        foreach ($troops_on_building as $unatropa) {

            $total = $unatropa->getTotal();

            for ($i = 0; $i < $total; $i++) {
                $unaTropa["troops_id"] = $unatropa->getTroops()->getId();
                $lista_tropas_defensor[] = $unaTropa;
            }

        }

        $this->eliminarTropas($lista_tropas_defensor, $porciento_a_eliminar);

    }

    //-------------------------------------------------------------------------------------------
    // Eliminar tropas
    //-------------------------------------------------------------------------------------------
    public function getListaTropasAeliminar($lista_tropas, $porciento_a_eliminar, &$lista_extendidad_tropas_a_eliminar, &$total_a_eliminar)
    {

        $lista_tropas_extendida = array();

        //Crear arreglo con un elemento por cada tropa
        foreach ($lista_tropas as $unatropa) {

            $total = $unatropa["total"];
            for ($i = 0; $i < $total; $i++) {
                $unatropa["total"] = 1;
                $lista_tropas_extendida[] = $unatropa;
            }
        }

        //Encontrando total a eliminar
        $total_tropas = count($lista_tropas_extendida);
        $total_a_eliminar = round(($porciento_a_eliminar * $total_tropas) / 100);
        //var_dump("Total tropas " . $total_tropas . " Total a eliminar " . $total_a_eliminar);

        //Reordenamiento aleatorio
        shuffle($lista_tropas_extendida);

        //Escogiendo las N primeras al azar
        $lista_extendidad_tropas_a_eliminar = array_slice($lista_tropas_extendida, 0, $total_a_eliminar);

    }

    //Dar lista agrupada por tipo de tropa y por usuario
    public function getListaCondensada($lista_extendidad_tropas_a_eliminar, &$lista_condensada)
    {
        $lista_condensada = array();
        foreach ($lista_extendidad_tropas_a_eliminar as $unatropa) {
            $total_tropas = count($lista_condensada);
            $encontrada = false;
            $indice = -1;
            for ($i = 0; $i < $total_tropas; $i++) {
                $unatropa_condensada =$lista_condensada[$i];
                if ($unatropa_condensada["troops_id"]==$unatropa["troops_id"]){
                    $encontrada = true;
                    $unatropa_condensada["total"]=$unatropa_condensada["total"]+1;
                    $indice = $i;
                    break;
                }
            }

            if ($encontrada) {
                $lista_condensada[$indice] = $unatropa_condensada;
            } else {
                $lista_condensada[] = $unatropa;
            }

        }

    }

}

/**
//----------------------------------------------------------------------------------------
// Eliminando tropas
//----------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------
// encontrar el total de tropas a eliminar de los atacantes
//------------------------------------------------------------------------------------------



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
// $castillo_del_atacante = $this->em->getRepository(Building::class)->find($id_castillo_atacante);
// $this->modificarBD($lista_tropas_eliminadas_atacante, $castillo_del_atacante );

 */
