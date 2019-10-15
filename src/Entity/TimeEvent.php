<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimeEventRepository")
 */
class TimeEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $EventType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $t_ini;

    /**
     * @ORM\Column(type="datetime")
     */
    private $t_ejec;

   
    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $extra_data;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventType(): ?EventType
    {
        return $this->EventType;
    }

    public function setEventType(?EventType $EventType): self
    {
        $this->EventType = $EventType;

        return $this;
    }

    public function getTIni(): ?\DateTimeInterface
    {
        return $this->t_ini;
    }

    public function setTIni(\DateTimeInterface $t_ini): self
    {
        $this->t_ini = $t_ini;

        return $this;
    }

    public function getTEjec(): ?\DateTimeInterface
    {
        return $this->t_ejec;
    }

    public function setTEjec(\DateTimeInterface $t_ejec): self
    {
        $this->t_ejec = $t_ejec;

        return $this;
    }

  

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getExtraData(): ?string
    {
        return $this->extra_data;
    }

    public function setExtraData(string $extra_data): self
    {
        $this->extra_data = $extra_data;

        return $this;
    }
}
