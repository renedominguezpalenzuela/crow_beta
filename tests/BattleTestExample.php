<?php

namespace App\Tests;

use App\Entity\Troop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BattleTestExample extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()    {
        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testTropas()    {
        $troop = $this->em->getRepository(Troop::class)->findAll();
        $this->assertCount(16, $troop);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()    {
        parent::tearDown();
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
    
}
