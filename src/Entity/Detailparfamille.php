<?php

namespace App\Entity;

use App\Repository\DetailparfamilleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DetailparfamilleRepository::class)
 */
class Detailparfamille extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="detailparfamilles")
     */
    private $famille;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="detailparfamilles")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisationparfamille::class, inversedBy="detailparfamilles")
     */
    private $cotisationparfamille;

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

    public function getFamille(): ?Famille
    {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;

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

    public function getCotisationparfamille(): ?Cotisationparfamille
    {
        return $this->cotisationparfamille;
    }

    public function setCotisationparfamille(?Cotisationparfamille $cotisationparfamille): self
    {
        $this->cotisationparfamille = $cotisationparfamille;

        return $this;
    }
}
