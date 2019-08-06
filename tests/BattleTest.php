<?php

namespace App\Tests;

use App\Entity\Troop;
use App\Service\Battle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BattleTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $battle;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();

        $this->battle = new Battle($this->em);
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    public function testTropas()
    {
        $troop = $this->em->getRepository(Troop::class)->findAll();
        $this->assertCount(16, $troop);
    }

    public function testgetOtherSideResults()
    {
        
        //Agotados los los tres casos de uso posibles
        //Victoria
        $victoria = $this->battle->getOtherSideResult(Battle::VICTORY);
        $this->assertEquals(Battle::DEFEAT, $victoria);

        //Derrota
        $defeat = $this->battle->getOtherSideResult(Battle::DEFEAT);
        $this->assertEquals(Battle::VICTORY, $defeat);

        //Stalemate
        $stalemate = $this->battle->getOtherSideResult(Battle::STALEMATE);
        $this->assertEquals(Battle::STALEMATE, $stalemate);
    }

   
    public function testgetRamdomBattleResultforAttacker()
    {
        
        //Agotados los los 4 casos de uso posibles

        //CASO 1: defender_troops = 0 and attacker_troops = 0
        //Resultado = -1
        $resultado = $this->battle->getRamdomBattleResultforAttacker(0,0);
        $this->assertEquals(Battle::UNDEFINED, $resultado, "Caso 1");

        //CASO 2: defender_troops <> 0 and attacker_troops <> 0
        //Resultado debe ser de 0 a 2
        $resultado = $this->battle->getRamdomBattleResultforAttacker(10,10);
        $this->assertGreaterThanOrEqual(0, $resultado, "Caso 2");
        $this->assertLessThanOrEqual(2, $resultado, "Caso 2");

        //CASO 3: defender_troops >= 0 and attacker_troops <= 0
        //resultado = 1
        $resultado = $this->battle->getRamdomBattleResultforAttacker(0,10);
        $this->assertEquals(Battle::DEFEAT, $resultado, "Caso 3");

        //CASO 4: defender_troops <= 0 and attacker_troops >= 0
        //resultado = 2
        $resultado = $this->battle->getRamdomBattleResultforAttacker(10,0);
        $this->assertEquals(Battle::VICTORY, $resultado, "Caso 4");

       
    }


    //TODO: verificar las porcientos calculados
    public function testgetPorcientos(){
        //Casos de uso -- BF > LF, BF < LF , BF = LF, BF = 0 and LF = 0, BF = 0 and LF <> 0,  BF <> 0 and LF = 0
        $defending_force_strength = 0;
        $attacking_force_strenght = 0;
        $defender_chance_of_victory =0;
        $attacker_chance_of_victory = 0;
        $staleChance = 0;
        $resultado_batalla_attacker =Battle::VICTORY;
        $porciento_tropas_eliminar_atacante = 0;
        $porciento_tropas_eliminar_defensor =0;

        $attacker_building_damage_strength = 3000;
        $danos_a_edificios = 0;


        //Caso 0: attacking_force= 0 AND defending_force = 0 
        //resultados todos 0                
          $this->battle->getPorcientos(0,0, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance, $resultado_batalla_attacker, $porciento_tropas_eliminar_atacante,$porciento_tropas_eliminar_defensor, $attacker_building_damage_strength,   $danos_a_edificios );
          $this->assertEquals(0, $defender_chance_of_victory, "Caso 0, defender"); 
          $this->assertEquals(0, $attacker_chance_of_victory, "Caso 0, attacker"); 
          $this->assertEquals(0, $staleChance, "Caso 0, staleChance");

        //Caso 1: attacking_force= 0 AND defending_force > 0                 
        //resultados defender = 100, attacker = 0, staleChance = 0
        $this->battle->getPorcientos(0,1000, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance , $resultado_batalla_attacker, $porciento_tropas_eliminar_atacante,$porciento_tropas_eliminar_defensor, $attacker_building_damage_strength,   $danos_a_edificios);

       
        $this->assertEquals(100, $defender_chance_of_victory, "Caso 1, defender"); 
        $this->assertEquals(0, $attacker_chance_of_victory, "Caso 1, attacker"); 
        $this->assertEquals(0, $staleChance, "Caso 1, staleChance");

        //Caso 2: attacking_force > 0 AND defending_force = 0                 
        //resultados defender = 0, attacker = 100, staleChance = 0
        $this->battle->getPorcientos(1000,0, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance,  $resultado_batalla_attacker, $porciento_tropas_eliminar_atacante,$porciento_tropas_eliminar_defensor, $attacker_building_damage_strength,   $danos_a_edificios );

     
        $this->assertEquals(0, $defender_chance_of_victory, "Caso 2, defender"); 
        $this->assertEquals(100, $attacker_chance_of_victory, "Caso 2, attacker"); 
        $this->assertEquals(0, $staleChance, "Caso 2, staleChance");

        //Caso 3: attacking_force > 0 AND defending_force > 0                 
        //resultados defender > 0, attacker > 0, staleChance > 0
        $this->battle->getPorcientos(1000,1000, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance,  $resultado_batalla_attacker, $porciento_tropas_eliminar_atacante,$porciento_tropas_eliminar_defensor, $attacker_building_damage_strength,   $danos_a_edificios );

//        var_dump($defender_chance_of_victory." ". $attacker_chance_of_victory." ". $staleChance);
        $this->assertEquals(33.33, $defender_chance_of_victory, "Caso 3, defender"); 
        $this->assertEquals(33.33, $attacker_chance_of_victory, "Caso 3, attacker"); 
        $this->assertEquals(33.33, $staleChance, "Caso 3, staleChance");
        $this->assertEquals(100, round($defender_chance_of_victory + $attacker_chance_of_victory + $staleChance), "Caso 3, suma");


         //Caso 4: attacking_force > 0 AND defending_force > 0                 
        //resultados defender > 0, attacker > 0, staleChance > 0
        $this->battle->getPorcientos(5000,1000, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance ,  $resultado_batalla_attacker, $porciento_tropas_eliminar_atacante,$porciento_tropas_eliminar_defensor, $attacker_building_damage_strength,   $danos_a_edificios);

      //var_dump($defender_chance_of_victory." ". $attacker_chance_of_victory." ". $staleChance);
        $this->assertEquals(14.29, $defender_chance_of_victory, "Caso 4, defender"); 
        $this->assertEquals(71.43, $attacker_chance_of_victory, "Caso 4, attacker"); 
        $this->assertEquals(14.29, $staleChance, "Caso 4, staleChance");
        $this->assertEquals(100, round($defender_chance_of_victory + $attacker_chance_of_victory + $staleChance), "Caso 4, suma");

    }

}
