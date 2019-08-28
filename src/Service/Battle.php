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
    public function getRamdomBattleResultforAttacker($attacking_force_strenght, $defending_force_strenght,
        $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance) {

        // +" "+ $defending_force_strenght +" "+
        //$attacker_chance_of_victory+" "+ $defender_chance_of_victory+" "+ $staleChance);

        $nAtacanteVictoria = round($attacker_chance_of_victory);
        $nDefenderVictoria = $nAtacanteVictoria + round($defender_chance_of_victory);
        $nStaleMate = $nDefenderVictoria + round($staleChance);

        /*   var_dump("Atacante de 0 a " +$nAtacanteVictoria );
        var_dump("Defensor de "+ $nAtacanteVictoria +" a " +$nDefenderVictoria );
        var_dump("Empate "+ $nDefenderVictoria +" a  100"  );*/

        $n100 = mt_rand(1, 100);

        /*
        si $n100 de 1 a $nAtacanteVictoria --> victoria del atacante
        si $n100 de $nAtacanteVictoria a $nAtacanteVictoria +$nDefenderVictoria  --> victoria del defensor
        si $n100 de $nDefenderVictoria a 100  --> stalemate
         */

        $resultado = Battle::UNDEFINED;

        if ($attacking_force_strenght > 0 && $defending_force_strenght > 0) {

            if ($n100 >= 1 && $n100 < $nAtacanteVictoria) {
                $resultado = Battle::VICTORY;
            }

            if ($n100 >= $nAtacanteVictoria && $n100 < $nDefenderVictoria) {
                $resultado = Battle::DEFEAT;
            }

            if ($n100 >= $nDefenderVictoria && $n100 <= 100) {
                $resultado = Battle::STALEMATE;
            }

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
    // Devuelve un numero al azar del 0 al 2 que simboliza uno de los resultados del ataque
    //-------------------------------------------------------------------------------------------------
    public function getRamdomBattleResultforAttackerOLD($attacking_force_strenght, $defending_force_strenght)
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

            $attacker_building_damage_strength = $attacker_building_damage_strength + $damage * $total;

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

    //--------------------------------------------------------------------------------------------
    // Calculo de Porcientos en funcion de las tropas de cada bando
    //--------------------------------------------------------------------------------------------

    public function getPorcientos($attacking_force, $defending_force,
        &$attacker_chance_of_victory, &$defender_chance_of_victory, &$staleChance
    ) {

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

        $BFCV = round($BFCV, 2);
        $LFCV = round($LFCV, 2);
        $staleChance = round($staleChance, 2);

        //&$defender_chance_of_victor, &$attacker_chance_of_victory, &$staleChance

        if ($defending_force > $attacking_force) {
            $defender_chance_of_victory = $BFCV;
            $attacker_chance_of_victory = $LFCV;
        } else {
            $defender_chance_of_victory = $LFCV;
            $attacker_chance_of_victory = $BFCV;
        }

        //realizar aproximacion dentro con un margen de 50 o 100 puntos

        $margen = 100; //TODO: modificar este valor para ver aumentar el error
        //grado de diferencia entre valores para considerarlos iguales
        $numeros_iguales = $this->compararNumerosconMargen($attacking_force, $defending_force, $margen);
        if ($numeros_iguales == true && $defending_force != 0 && $attacking_force != 0) {
            $staleChance = 33.34;
            //$BFCV = 10;
            //$LFCV = 10;
            $defender_chance_of_victory = 33.33;
            $attacker_chance_of_victory = 33.33;
        }
    }

    //Compara dos numeros con un margen de error
    public function compararNumerosconMargen($N1, $N2, $Margen)
    {
        $resultado = false;

        $nivel_inferior = $N2 - $Margen;
        if ($nivel_inferior < 0) {
            $nivel_inferior = 0;}

        $nivel_superior = $N2 + $Margen;

        if ($nivel_inferior <= $N1 && $nivel_superior >= $N1) {
            $resultado = true;
        }

        return $resultado;

    }

    public function getPorcientoTropasaEliminar($attacker_chance_of_victory, $defender_chance_of_victory, $staleChance, $resultado_batalla_attacker, &$porciento_tropas_eliminar_atacante, &$porciento_tropas_eliminar_defensor)
    {

        if ($resultado_batalla_attacker != Battle::STALEMATE) {

            $porciento_tropas_eliminar_atacante = $defender_chance_of_victory;
            $porciento_tropas_eliminar_defensor = $attacker_chance_of_victory;

        } else {
            $porciento_tropas_eliminar_atacante = $staleChance;
            $porciento_tropas_eliminar_defensor = $staleChance;
        }

    }

    public function getDannos_a_edificios($resultado_batalla_attacker, $attacker_building_damage_strength)
    {

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

        return $danos_a_edificios;

    }

    //Casos de uso

    //1) DEF_F > ATTACK_F
    //2) DEF_F < ATTACK_F
    //3) DEF_F  = ATTACK_F
    //4) DEF_F = 0 and ATTACK_F = 0
    //5) DEF_F = 0 and ATTACK_F <> 0
    //6) DEF_F <> 0 and ATTACK_F = 0

    public function getPorcientosOLD($attacking_force, $defending_force, &$attacker_chance_of_victory, &$defender_chance_of_victory, &$staleChance,
        $resultado_batalla_attacker, &$porciento_tropas_eliminar_atacante, &$porciento_tropas_eliminar_defensor,
        $attacker_building_damage_strength, &$danos_a_edificios) {

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

    //-------------------------------------------------------------------------------------------
    // Eliminar tropas
    //-------------------------------------------------------------------------------------------
    public function getListaTropasAeliminar($lista_tropas, $porciento_a_eliminar, &$lista_extendidad_tropas_a_eliminar, &$total_a_eliminar, &$total_inicial, &$total_final)
    {

        $lista_tropas_extendida = array();
        $total_inicial = 0;

        //Crear arreglo con un elemento por cada tropa
        foreach ($lista_tropas as $unatropa) {

            $total = $unatropa["total"];
            $total_inicial = $total_inicial +  $total;
            for ($i = 0; $i < $total; $i++) {
                $unatropa["total"] = 1;
                $lista_tropas_extendida[] = $unatropa;
            }
        }

        //Encontrando total a eliminar
        $total_tropas = count($lista_tropas_extendida);
        $total_a_eliminar = round(($porciento_a_eliminar * $total_tropas) / 100);

        $total_final =  $total_inicial - $total_a_eliminar;
        if ($total_final<0) $total_final=0;
        

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
                $unatropa_condensada = $lista_condensada[$i];
                if ($unatropa_condensada["troops_id"] == $unatropa["troops_id"]) {
                    $encontrada = true;
                    $unatropa_condensada["total"] = $unatropa_condensada["total"] + 1;
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

    //Dar lista agrupada por tipo de tropa y por usuario

    /*array (size=2)
  0 => 
    array (size=4)
      'troops_id' => int 5
      'name' => string 'Archers' (length=7)
      'total' => int 4
      'user' => string 'azul' (length=4)
  1 => 
    array (size=4)
      'troops_id' => int 9
      'name' => string 'Archers' (length=7)
      'total' => int 2
      'user' => string 'azul1' (length=5)*/

  
    public function eliminarTroopsFromDB($lista_condensada_tropas_a_eliminar, $edificio)
    {

        //echo "Batle \n";
        //var_dump($lista_condensada_tropas_a_eliminar);
        //Recorro la lista

        //busco la tropa en BUILDINGS -- RESTO EL TOTAL (SI LLEGA A CERO LA ELIMINO)
        //BUSCO LA TROPA EN TROOPS -- RESTO EL TOTAL (SI LLEGA A CERO LA ELIMINO)

        // $this->em = $this->getDoctrine()->getManager();

        foreach ($lista_condensada_tropas_a_eliminar as $una_tropa) {

        

            //Total a eliminar
            $total_eliminar = $una_tropa["total"];

            $troop_id = $una_tropa["troops_id"];

           
            //-------------------------------------------
            // Total en el edificio
            //-------------------------------------------
            
            //Busco la tropa en el edificio
            $tropa_edificio = $this->em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id, 'building' => $edificio]);

            $total_edificio = $tropa_edificio->getTotal();

            //Total final en edificio
            $total_final_edificio = $total_edificio - $total_eliminar;

             //Modificar BD

             if ($total_final_edificio <= 0) {
                $total_final_edificio = 0;
                $this->em->remove($tropa_edificio);
                $this->em->flush();
            } else {
                $tropa_edificio->setTotal($total_final_edificio);
                $this->em->persist($tropa_edificio);
            }


            //-------------------------------------------
            // Total en Troops
            //-------------------------------------------

             //busco la tropa en Troops
            $tropa_Troops = $this->em->getRepository(Troop::class)->find($troop_id);

            $total_Troops = $tropa_Troops->getTotal();

            //total final en Troops
            $total_final_Troops = $total_Troops - $total_eliminar;

          
            //Modificar BD
            if ($total_final_Troops <= 0) {
                $total_final_Troops = 0;
                $this->em->remove($tropa_Troops);   
                $this->em->flush();           
            } else {
                $tropa_Troops->setTotal($total_final_Troops);
                $this->em->persist($tropa_Troops);
            }

        }

        $this->em->flush();

    }


    public function calcularPuntos ($resultado_ataque, $bajas_al_enemigo) {

        $puntos_obtenidos = 0;

         //$porcientos_danos_a_edificios
         switch ($resultado_ataque) {
            case self::VICTORY:
                $puntos_obtenidos = 100 +  $bajas_al_enemigo;
                break;
            case self::DEFEAT:
                $puntos_obtenidos = 0;
                break;
            case self::STALEMATE:
                $puntos_obtenidos = 0;
                break;
            default:
                $puntos_obtenidos = 0;
        }

       // echo "Puntos ".$puntos_obtenidos." ".$resultado_ataque; 
        

       return $puntos_obtenidos;
    }


    public function EscribirPuntosUsuario ($puntos, $usuario) {
        if ($puntos<=0) {return;}
        $puntos_iniciales = $usuario->getUserpoints();

        $usuario->setUserpoints($puntos_iniciales+$puntos);
        $this->em->persist($usuario);
        $this->em->flush();
    }

    public function EscribirPuntosKingdom ($puntos, $kingdom, $building_taken) {
        if ($puntos<=0) {return;}
        $puntos_iniciales = $kingdom->getKingdomPoints();

        //definir si el edificio tomado es un castillo o un camp
        //pasar como parametro el edificio atacado, para determinar el tipo
        if ($building_taken) {
           // $puntos = $puntos +  
        }

        $kingdom->setKingdomPoints($puntos_iniciales+$puntos);
        $this->em->persist($kingdom);
        $this->em->flush();


    }

}

