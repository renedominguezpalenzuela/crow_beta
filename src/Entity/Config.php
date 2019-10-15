<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $testing = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gold_ini = 500000;


      /**
     * @ORM\Column(type="string", length=255)
     */
    private $test_user='axl';

    /**
     * @ORM\Column(type="integer")
     */
    private $gold_increment;



    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $t_last_clock_check;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTesting(): ?bool
    {
        return $this->testing;
    }

    public function setTesting(bool $testing): self
    {
        $this->testing = $testing;

        return $this;
    }

    public function getGoldIni(): ?int
    {
        return $this->gold_ini;
    }

    public function setGoldIni(?int $gold_ini): self
    {
        $this->gold_ini = $gold_ini;

        return $this;
    }

    /**
     * Get the value of test_user
     */ 
    public function getTest_user()
    {
        return $this->test_user;
    }

    /**
     * Set the value of test_user
     *
     * @return  self
     */ 
    public function setTest_user($test_user)
    {
        $this->test_user = $test_user;

        return $this;
    }

    public function getGoldIncrement(): ?int
    {
        return $this->gold_increment;
    }

    public function setGoldIncrement(int $gold_increment): self
    {
        $this->gold_increment = $gold_increment;

        return $this;
    }


    public function getTLastClockCheck(): ?\DateTimeInterface
    {
        return $this->t_last_clock_check;
    }

    public function setTLastClockCheck(?\DateTimeInterface $t_last_clock_check): self
    {
        $this->t_last_clock_check = $t_last_clock_check;

        return $this;
    }
}
