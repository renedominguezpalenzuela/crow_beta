<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuildingRepository")
 */
class Building
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="BuildingType")
     */
    private $buildingType;

    /**
     * @ORM\Column(type="integer")
     */
    private $defenseRemaining;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="TroopBuilding", mappedBy="building")
     */
    private $troopBuildings;

    public function __construct()
    {
        $this->troopBuildings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefenseRemaining(): ?int
    {
        return $this->defenseRemaining;
    }

    public function setDefenseRemaining(int $defenseRemaining): self
    {
        $this->defenseRemaining = $defenseRemaining;

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

    public function getBuildingType(): ?BuildingType
    {
        return $this->buildingType;
    }

    public function setBuildingType(?BuildingType $buildingType): self
    {
        $this->buildingType = $buildingType;

        return $this;
    }

    /**
     * @return Collection|TroopBuilding[]
     */
    public function getTroopBuildings(): Collection
    {
        return $this->troopBuildings;
    }

    public function addTroopBuilding(TroopBuilding $troopBuilding): self
    {
        if (!$this->troopBuildings->contains($troopBuilding)) {
            $this->troopBuildings[] = $troopBuilding;
            $troopBuilding->setBuilding($this);
        }

        return $this;
    }

    public function removeTroopBuilding(TroopBuilding $troopBuilding): self
    {
        if ($this->troopBuildings->contains($troopBuilding)) {
            $this->troopBuildings->removeElement($troopBuilding);
            // set the owning side to null (unless already changed)
            if ($troopBuilding->getBuilding() === $this) {
                $troopBuilding->setBuilding(null);
            }
        }

        return $this;
    }
}
