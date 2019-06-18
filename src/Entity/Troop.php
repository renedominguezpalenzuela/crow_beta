<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TroopRepository")
 */
class Troop
{

    public function __construct()
    {
        $this->level = 0;
        $this->buildings = new ArrayCollection();
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @var
     * @ORM\Column(name="attack", type="integer")
     */
    private $attack;

    /**
     * @var
     * @ORM\Column(name="defense", type="integer")
     */
    private $defense;

    /**
     * @var
     * @ORM\Column(name="damage", type="integer")
     */
    private $damage;

    /**
     * @var
     * @ORM\Column(name="speed", type="integer")
     */
    private $speed;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="UnitType")
     */
    private $unitType;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="TroopBuilding", mappedBy="troops")
     */
    private $buildings;

    /**
     * @ORM\Column(type="integer")
     */
    private $unit_type_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
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

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getDamage(): ?int
    {
        return $this->damage;
    }

    public function setDamage(int $damage): self
    {
        $this->damage = $damage;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

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

    /**
     * @return Collection|TroopBuilding[]
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function addBuilding(TroopBuilding $building): self
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings[] = $building;
            $building->setTroops($this);
        }

        return $this;
    }

    public function removeBuilding(TroopBuilding $building): self
    {
        if ($this->buildings->contains($building)) {
            $this->buildings->removeElement($building);
            // set the owning side to null (unless already changed)
            if ($building->getTroops() === $this) {
                $building->setTroops(null);
            }
        }

        return $this;
    }

    public function getUnitType(): ?UnitType
    {
        return $this->unitType;
    }

    public function setUnitType(?UnitType $unitType): self
    {
        $this->unitType = $unitType;

        return $this;
    }

    public function getUnitTypeId(): ?int
    {
        return $this->unit_type_id;
    }

    public function setUnitTypeId(int $unit_type_id): self
    {
        $this->unit_type_id = $unit_type_id;

        return $this;
    }
}
