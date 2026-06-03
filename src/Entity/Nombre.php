<?php

namespace App\Entity;

use App\Repository\NombreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NombreRepository::class)
 */
class Nombre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $homme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $femme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $garcon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $enfant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $adulte;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="nombres")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomme(): ?int
    {
        return $this->homme;
    }

    public function setHomme(?int $homme): self
    {
        $this->homme = $homme;

        return $this;
    }

    public function getFemme(): ?int
    {
        return $this->femme;
    }

    public function setFemme(?int $femme): self
    {
        $this->femme = $femme;

        return $this;
    }

    public function getFille(): ?int
    {
        return $this->fille;
    }

    public function setFille(?int $fille): self
    {
        $this->fille = $fille;

        return $this;
    }

    public function getGarcon(): ?int
    {
        return $this->garcon;
    }

    public function setGarcon(?int $garcon): self
    {
        $this->garcon = $garcon;

        return $this;
    }

    public function getEnfant(): ?int
    {
        return $this->enfant;
    }

    public function setEnfant(?int $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }

    public function getAdulte(): ?int
    {
        return $this->adulte;
    }

    public function setAdulte(?int $adulte): self
    {
        $this->adulte = $adulte;

        return $this;
    }

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }
}
