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
     * @ORM\Column(name="gold", type="integer")
     * @Assert\NotBlank()
     */
    private $gold;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Kingdom")
     */
    private $kingdom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGold(): ?int
    {
        return $this->gold;
    }

    public function setGold(int $gold): self
    {
        $this->gold = $gold;

        return $this;
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
