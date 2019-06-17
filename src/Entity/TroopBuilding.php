<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TroopBuildingRepository")
 */
class TroopBuilding
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Troop", inversedBy="troopBuildings")
     */
    private $troops;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Building", inversedBy="troopBuildings")
     */
    private $building;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getTroops(): ?Troop
    {
        return $this->troops;
    }

    public function setTroops(?Troop $troops): self
    {
        $this->troops = $troops;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

        return $this;
    }
}
