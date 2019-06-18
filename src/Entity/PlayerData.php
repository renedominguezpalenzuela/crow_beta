<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerDataRepository")
 */
class PlayerData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $gold;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    public function __construct()
    {
        $this->gold = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGold(): ?float
    {
        return $this->gold;
    }

    public function setGold(float $gold): self
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
}
