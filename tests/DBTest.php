<?php

namespace App\Tests;

use App\Entity\Troop;
use App\Service\Battle;
use App\Service\Datos;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DBTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $battle;
    private $db;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
       // $this->battle = new Battle($this->em);

       $this->db = new Datos($this->em);

        $this->setupInitalBDState();
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->db = null;
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }


    public function setupInitalBDState(){
      //borrar todas las tablas, crear todos los datos necesarios
      $this->db->borrarDatos();

    }
    public function testInicial()
    {
       // $troop = $this->em->getRepository(Troop::class)->findAll();
        $this->assertTrue(true, "Probando BD");
    }
}