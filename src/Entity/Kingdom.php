<?php

namespace App\Entity;

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
    private $idKingdomBoss;

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
}
