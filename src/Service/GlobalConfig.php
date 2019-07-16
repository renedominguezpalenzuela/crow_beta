<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Config;


/**
 * Class CreateInitialUserData
 *
 *
 */

 //Ultima version 22-06-2019  2:15 pm
class GlobalConfig
{

    private $em;
    private $config;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        //La primera tupla es la de config
        $this->config = $this->em->getRepository(Config::class)->findAll()[0];
    }


    //Lee de la Base de datos Config.testing
    //true -- es test mode
    //false -- modo produccion
    public function isTestMode(){
        $test_mode=false;
        $test_mode = $this->config->getTesting();       
        return $test_mode;
    }


    public function getTest_User(){
      
        $test_user = $this->config->getTest_user();       
        return $test_user;
    }

    public function getUserInitialGold(){
        $gold = 0;
        $gold = $this->config->getGoldIni();
        return $gold;
    }


}