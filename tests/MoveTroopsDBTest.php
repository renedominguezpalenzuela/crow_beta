<?php

namespace App\Tests;

use App\Entity\Troop;
use App\Entity\User;
use App\Entity\Building;
use App\Entity\TroopBuilding;
use App\Service\Battle;
use App\Service\Datos;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Kingdom;

use Symfony\Component\Console\Tester\CommandTester;


class MoveTroopsDBTest extends KernelTestCase
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
        

        $this->setupInitalBDState();
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


    //Precondicion haber creado datos iniciales con DBInitialSetupTest.php
    public function setupInitalBDState(){

       $this->moverAllTropasAlCastillo("axl");
       $this->moverAllTropasAlCastillo("azul1");
       $this->moverAllTropasAlCastillo("azul");
          
    }

    public function moverAllTropasAlCastillo($usuario_name){
        //Buscar todas las tropas de axl en troops
        //moverlas en troop_building, desde barracks a castle
        //usuario
        $user = $this->em->getRepository(User::class)->findOneBy(['name' => $usuario_name]);
        
        //lista tropas
        $tropas = $this->em->getRepository(Troop::class)->findBy(['user' => $user->getId()]);

        //buscar el id del castillo
        $id_castillo = $user->getKingdom()->getMainCastleId();
        $castillo =  $this->em->getRepository(Building::class)->findOneBy(['id' =>$id_castillo]);

       /* echo "\n";
        echo "id Castillo ".$id_castillo;*/

        //Cada una de estas tropas la busco un troop_building y modifico su ubicacion al castillo

        foreach ($tropas as $unatropa) {
            $troop_building =  $this->em->getRepository(TroopBuilding::class)->findOneBy(['troops' =>$unatropa->getId()]);

            if ($troop_building) {
               // public function setBuilding(?Building $building): self

                $troop_building->setBuilding($castillo);
                $this->em->persist($troop_building);
                $this->em->flush();
            }


        }


        //Buscar todas las tropas de azul en troops
        //moverlas en troop_building, desde barracks a castle

  }


    public function testInicial()
    {
        
       //2) Atacar castillo azul
       //2.1) sin tropas
       //2.2) con todas las tropas
        $this->assertTrue(true, "Probando BD");
    }

    public function testMoverTropas(){


        $lista_condensada_tropas_a_eliminar = array();
        $edificio =3; //Castillo azul


        $troop_id1 = 5;
        $troop_id2 = 9;

        $lista_condensada_tropas_a_eliminar[0]["troops_id"] =$troop_id1;
        $lista_condensada_tropas_a_eliminar[0]["name"] ="Archer";
        $lista_condensada_tropas_a_eliminar[0]["total"] =10;
        $lista_condensada_tropas_a_eliminar[0]["user"] ="Azul";

        $lista_condensada_tropas_a_eliminar[1]["troops_id"] =$troop_id2;
        $lista_condensada_tropas_a_eliminar[1]["name"] ="Archer";
        $lista_condensada_tropas_a_eliminar[1]["total"] =5;
        $lista_condensada_tropas_a_eliminar[1]["user"] ="Azul1";

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


        $this->battle->eliminarTroopsFromDB($lista_condensada_tropas_a_eliminar, $edificio);

        //Prueba consultar las tropas del edificio y verificar que son las correctas
        //$this->assertTrue(false, "Probando BD");

        $tropa = $this->em->getRepository(TroopBuilding::class)->findOneBy(['troops' => $troop_id1, 'building' => $edificio]);
        $this->assertEquals($tropa->getTotal(), 190, "Caso 1-1");

        $tropa = $this->em->getRepository(Troop::class)->findOneBy(['id' => $troop_id1]);
        $this->assertEquals($tropa->getTotal(), 190, "Caso 1-2");

    }


}
