<?php

namespace App\Entity;

use App\Repository\DetailpargroupeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DetailpargroupeRepository::class)
 */
class Detailpargroupe extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="detailpargroupes")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="detailpargroupes")
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisationpargroupe::class, inversedBy="detailpargroupes")
     */
    private $cotisationpargroupe;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getCotisationpargroupe(): ?Cotisationpargroupe
    {
        return $this->cotisationpargroupe;
    }

    public function setCotisationpargroupe(?Cotisationpargroupe $cotisationpargroupe): self
    {
        $this->cotisationpargroupe = $cotisationpargroupe;

        return $this;
    }
}
