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
}
