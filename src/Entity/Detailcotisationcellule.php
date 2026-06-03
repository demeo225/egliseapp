<?php

namespace App\Entity;

use App\Repository\DetailcotisationcelluleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DetailcotisationcelluleRepository::class)
 */
class Detailcotisationcellule extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datedetail;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montantpayer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reste;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="detailcotisationcellules", cascade={"persist"})
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class)
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisationcellule::class, cascade={"persist"})
     */
    private $cotisationcellule;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisercellule::class, inversedBy="detailcotisationcellules", cascade={"persist"})
     */
    private $cotisercellule;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedetail(): ?\DateTimeInterface
    {
        return $this->datedetail;
    }

    public function setDatedetail(?\DateTimeInterface $datedetail): self
    {
        $this->datedetail = $datedetail;

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

    public function getMontantpayer(): ?int
    {
        return $this->montantpayer;
    }

    public function setMontantpayer(?int $montantpayer): self
    {
        $this->montantpayer = $montantpayer;

        return $this;
    }

    public function getReste(): ?int
    {
        return $this->reste;
    }

    public function setReste(?int $reste): self
    {
        $this->reste = $reste;

        return $this;
    }

    public function getFidele(): ?Fidele
    {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self
    {
        $this->fidele = $fidele;

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

    public function getCellule(): ?Cellule
    {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self
    {
        $this->cellule = $cellule;

        return $this;
    }

    public function getCotisationcellule(): ?Cotisationcellule
    {
        return $this->cotisationcellule;
    }

    public function setCotisationcellule(?Cotisationcellule $cotisationcellule): self
    {
        $this->cotisationcellule = $cotisationcellule;

        return $this;
    }

    public function getCotisercellule(): ?Cotisercellule
    {
        return $this->cotisercellule;
    }

    public function setCotisercellule(?Cotisercellule $cotisercellule): self
    {
        $this->cotisercellule = $cotisercellule;

        return $this;
    }
}
