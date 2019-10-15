<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventTypeRepository")
 */
class EventType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $periodic;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t_ejec_d;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t_ejec_h;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t_ejec_m;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $t_ejec_s;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $explanation = [];

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

    public function getPeriodic(): ?bool
    {
        return $this->periodic;
    }

    public function setPeriodic(bool $periodic): self
    {
        $this->periodic = $periodic;

        return $this;
    }

    public function getTEjecD(): ?int
    {
        return $this->t_ejec_d;
    }

    public function setTEjecD(?int $t_ejec_d): self
    {
        $this->t_ejec_d = $t_ejec_d;

        return $this;
    }

    public function getTEjecH(): ?int
    {
        return $this->t_ejec_h;
    }

    public function setTEjecH(?int $t_ejec_h): self
    {
        $this->t_ejec_h = $t_ejec_h;

        return $this;
    }

    public function getTEjecM(): ?int
    {
        return $this->t_ejec_m;
    }

    public function setTEjecM(?int $t_ejec_m): self
    {
        $this->t_ejec_m = $t_ejec_m;

        return $this;
    }

    public function getTEjecS(): ?int
    {
        return $this->t_ejec_s;
    }

    public function setTEjecS(?int $t_ejec_s): self
    {
        $this->t_ejec_s = $t_ejec_s;

        return $this;
    }

    public function getExplanation(): ?array
    {
        return $this->explanation;
    }

    public function setExplanation(?array $explanation): self
    {
        $this->explanation = $explanation;

        return $this;
    }
}
