<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KingdomRepository")
 * @UniqueEntity(fields={"name"})
 */
class Kingdom
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @var
     * @ORM\Column(name="id_kingdom_boss", type="integer")
     * @Assert\NotBlank()
     */
    private $idKingdomBoss = 0;

   

  /**
     * @ORM\Column(type="string", length=255)
     */

     
    private $color_class = "card-header-primary";
    //card-header-primary (purple) | info (blue) | success (green) | warning(orange) | danger(red) | rose(pink)



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Building", mappedBy="kingdom")
     */
    private $buildings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="kingdom")
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $kingdom_points=0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $officer1=0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $officer2=0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $officer3=0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $officer4=0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $officer5=0;

    /**
     * @ORM\Column(type="integer")
     */
    private $main_castle_id=0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gold;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

  

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIdKingdomBoss(): ?int
    {
        return $this->idKingdomBoss;
    }

    public function setIdKingdomBoss(int $idKingdomBoss): self
    {
        $this->idKingdomBoss = $idKingdomBoss;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection|Building[]
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function addBuilding(Building $building): self
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings[] = $building;
            $building->setKingdom($this);
        }

        return $this;
    }

    public function removeBuilding(Building $building): self
    {
        if ($this->buildings->contains($building)) {
            $this->buildings->removeElement($building);
            // set the owning side to null (unless already changed)
            if ($building->getKingdom() === $this) {
                $building->setKingdom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setKingdom($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getKingdom() === $this) {
                $user->setKingdom(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of color_class
     */ 
    public function getColor_class()
    {
        return $this->color_class;
    }

    /**
     * Set the value of color_class
     *
     * @return  self
     */ 
    public function setColor_class($color_class)
    {
        $this->color_class = $color_class;

        return $this;
    }

    public function getKingdomPoints(): ?int
    {
        return $this->kingdom_points;
    }

    public function setKingdomPoints(?int $kingdom_points): self
    {
        $this->kingdom_points = $kingdom_points;

        return $this;
    }

    public function getOfficer1(): ?int
    {
        return $this->officer1;
    }

    public function setOfficer1(?int $officer1): self
    {
        $this->officer1 = $officer1;

        return $this;
    }

    public function getOfficer2(): ?int
    {
        return $this->officer2;
    }

    public function setOfficer2(?int $officer2): self
    {
        $this->officer2 = $officer2;

        return $this;
    }

    public function getOfficer3(): ?int
    {
        return $this->officer3;
    }

    public function setOfficer3(?int $officer3): self
    {
        $this->officer3 = $officer3;

        return $this;
    }

    public function getOfficer4(): ?int
    {
        return $this->officer4;
    }

    public function setOfficer4(?int $officer4): self
    {
        $this->officer4 = $officer4;

        return $this;
    }

    public function getOfficer5(): ?int
    {
        return $this->officer5;
    }

    public function setOfficer5(?int $officer5): self
    {
        $this->officer5 = $officer5;

        return $this;
    }

    public function getMainCastleId(): ?int
    {
        return $this->main_castle_id;
    }

    public function setMainCastleId(int $main_castle_id): self
    {
        $this->main_castle_id = $main_castle_id;

        return $this;
    }

    public function getGold(): ?int
    {
        return $this->gold;
    }

    public function setGold(?int $gold): self
    {
        $this->gold = $gold;

        return $this;
    }
}
