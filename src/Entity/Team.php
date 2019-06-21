<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @var
     * @ORM\OneToOne(targetEntity="User", inversedBy="team")
     */
    private $user;
    //tiene que ser unico

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Kingdom")
     */
    private $kingdom;

    public function getId(): ?int
    {
        return $this->id;
    }

   
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getKingdom(): ?Kingdom
    {
        return $this->kingdom;
    }

    public function setKingdom(?Kingdom $kingdom): self
    {
        $this->kingdom = $kingdom;

        return $this;
    }

    public function __toString()
    {
        return $this->getKingdom()->getName();
    }
}
