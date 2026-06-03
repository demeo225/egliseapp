<?php

namespace App\Entity;

use App\Repository\SoldeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SoldeRepository::class)
 */
class Solde
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

  
    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="soldes")
     */
    private $eglise;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }
}
