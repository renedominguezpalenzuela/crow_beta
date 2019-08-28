<?php

namespace App\Tests;

use App\Entity\Troop;
use App\Service\Battle;
use App\Service\Datos;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Kingdom;

use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;


class DBTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $battle;
    private $db;
    private $encoder;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->encoder = $kernel->getContainer()->get("security.password_encoder");
        $this->db = new Datos($this->em,  $this->encoder);

        exec("php bin/console doctrine:database:create");
        exec("php bin/console doctrine:schema:update --force");
        


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

    //-----------------------------------------------------------------------------------------
    //borrar todas las tablas, crear todos los datos necesarios
    //-----------------------------------------------------------------------------------------
    public function setupInitalBDState(){
      $this->db->borrarDatos();
      $this->db->addAllData();     
    }

    public function testInicial()
    {
        $this->assertTrue(true, "Probando BD");
    }
}