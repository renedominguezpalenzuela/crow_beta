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
        
      $attacker_chance_of_victory = 70;
        $defender_chance_of_victory =20;
        $staleChance = 10;
        //Agotados los los 4 casos de uso posibles
        //CASO 1: defender_troops = 0 and attacker_troops = 0
        //Resultado = -1
        $resultado = $this->battle->getRamdomBattleResultforAttacker(0,0,
                                            $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
       $this->assertEquals(Battle::UNDEFINED, $resultado, "Caso 1");

        //CASO 2: defender_troops <> 0 and attacker_troops <> 0
        //Resultado debe ser de 0 a 2
        $resultado = $this->battle->getRamdomBattleResultforAttacker(10,10,
                                            $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
        $this->assertGreaterThanOrEqual(0, $resultado, "Caso 2");
        $this->assertLessThanOrEqual(2, $resultado, "Caso 2");

        //CASO 3: defender_troops >= 0 and attacker_troops <= 0
        //resultado = 1
        $resultado = $this->battle->getRamdomBattleResultforAttacker(0,10 , 
                                           $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
        $this->assertEquals(Battle::DEFEAT, $resultado, "Caso 3");

        //CASO 4: defender_troops <= 0 and attacker_troops >= 0
        //resultado = 2
        $resultado = $this->battle->getRamdomBattleResultforAttacker(10,0,
                                           $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
        $this->assertEquals(Battle::VICTORY, $resultado, "Caso 4");

        //Gana el atacante el 95% de las veces
        $attacker_chance_of_victory = 90;
        $defender_chance_of_victory =5;
        $staleChance = 5;

        $resultado = $this->battle->getRamdomBattleResultforAttacker(10,10,
        $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);

        $this->assertEquals(Battle::VICTORY, $resultado, "getRamdomBattleResultforAttacker Caso 5");
        

          //Gana el defensor el 95% de las veces
        
          $attacker_chance_of_victory = 5;
          $defender_chance_of_victory =95;
          $staleChance = 5;
  
          $resultado = $this->battle->getRamdomBattleResultforAttacker(10,10,
                                             $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
          $this->assertEquals(Battle::DEFEAT, $resultado, "getRamdomBattleResultforAttacker Caso 6");
          
             //tablas      
             $attacker_chance_of_victory = 33;
             $defender_chance_of_victory =33;
             $staleChance = 33;
     
             $resultado = $this->battle->getRamdomBattleResultforAttacker(10,10,
                                                $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
             $this->assertEquals(Battle::STALEMATE, $resultado, "getRamdomBattleResultforAttacker Caso 7");
                   
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
        $this->battle->getPorcientos(0,0, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);
        $this->assertEquals(0, $defender_chance_of_victory, "Caso 0, defender"); 
        $this->assertEquals(0, $attacker_chance_of_victory, "Caso 0, attacker"); 
        $this->assertEquals(0, $staleChance, "Caso 0, staleChance");

        //Caso 1: attacking_force= 0 AND defending_force > 0                 
        //resultados defender = 100, attacker = 0, staleChance = 0
        $this->battle->getPorcientos(0,1000, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);

       
        $this->assertEquals(100, $defender_chance_of_victory, "Caso 1, defender"); 
        $this->assertEquals(0, $attacker_chance_of_victory, "Caso 1, attacker"); 
        $this->assertEquals(0, $staleChance, "Caso 1, staleChance");

        //Caso 2: attacking_force > 0 AND defending_force = 0                 
        //resultados defender = 0, attacker = 100, staleChance = 0
        $this->battle->getPorcientos(1000,0, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);

     
        $this->assertEquals(0, $defender_chance_of_victory, "Caso 2, defender"); 
        $this->assertEquals(100, $attacker_chance_of_victory, "Caso 2, attacker"); 
        $this->assertEquals(0, $staleChance, "Caso 2, staleChance");

        //Caso 3: attacking_force > 0 AND defending_force > 0                 
        //resultados defender > 0, attacker > 0, staleChance > 0
        $this->battle->getPorcientos(1000,1000, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance);

//        var_dump($defender_chance_of_victory." ". $attacker_chance_of_victory." ". $staleChance);
        $this->assertEquals(33.33, $defender_chance_of_victory, "Caso 3, defender"); 
        $this->assertEquals(33.33, $attacker_chance_of_victory, "Caso 3, attacker"); 
        $this->assertEquals(33.34, $staleChance, "Caso 3, staleChance");
        $this->assertEquals(100, round($defender_chance_of_victory + $attacker_chance_of_victory + $staleChance), "Caso 3, suma");


         //Caso 4: attacking_force > 0 AND defending_force > 0                 
        //resultados defender > 0, attacker > 0, staleChance > 0
        $this->battle->getPorcientos(5000,1000, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance );

      //var_dump($defender_chance_of_victory." ". $attacker_chance_of_victory." ". $staleChance);
        $this->assertEquals(14.29, $defender_chance_of_victory, "Caso 4, defender"); 
        $this->assertEquals(71.43, $attacker_chance_of_victory, "Caso 4, attacker"); 
        $this->assertEquals(14.29, $staleChance, "Caso 4, staleChance");
        $this->assertEquals(100, round($defender_chance_of_victory + $attacker_chance_of_victory + $staleChance), "Caso 4, suma");

           //Caso 4: attacking_force > 0 AND defending_force > 0                 
        //resultados defender > 0, attacker > 0, staleChance > 0
        $this->battle->getPorcientos(5000,4901, $attacker_chance_of_victory, $defender_chance_of_victory, $staleChance );

      //var_dump($defender_chance_of_victory." ". $attacker_chance_of_victory." ". $staleChance);
        $this->assertEquals(33.33, $defender_chance_of_victory, "Caso 5, defender"); 
        $this->assertEquals(33.33, $attacker_chance_of_victory, "Caso 5, attacker"); 
        $this->assertEquals(33.34, $staleChance, "Caso 5, staleChance");
        $this->assertEquals(100, round($defender_chance_of_victory + $attacker_chance_of_victory + $staleChance), "Caso 4, suma");


    }

    public function testCompararDosNumeroConMargen(){


        $margen  = 3;
        $n1 = 3;
        $n2 = 4;

        $resultado = $this->battle->compararNumerosconMargen($n1,$n2,$margen);
        $this->assertEquals(true, $resultado, "Caso 1: compararNumerosconMargen");


        
        $margen  = 3;
        $n1 = 30;
        $n2 = 4;

        $resultado = $this->battle->compararNumerosconMargen($n1,$n2,$margen);
        $this->assertEquals(false, $resultado, "Caso 2: compararNumerosconMargen");

        $margen  = 3;
        $n1 = 30;
        $n2 = 30;

        $resultado = $this->battle->compararNumerosconMargen($n1,$n2,$margen);
        $this->assertEquals(true, $resultado, "Caso 3: compararNumerosconMargen");


        $margen  = 3;
        $n1 = 0;
        $n2 = 0;

        $resultado = $this->battle->compararNumerosconMargen($n1,$n2,$margen);
        $this->assertEquals(true, $resultado, "Caso 3: compararNumerosconMargen");

        $margen  = 3;
        $n1 = 0;
        $n2 = 10;

        $resultado = $this->battle->compararNumerosconMargen($n1,$n2,$margen);
        $this->assertEquals(false, $resultado, "Caso 4: compararNumerosconMargen");


    }

}
