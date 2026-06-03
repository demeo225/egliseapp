<?php

namespace App\Entity;

use App\Repository\DetailcotisationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DetailcotisationRepository::class)
 */
class Detailcotisation extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="detailcotisations", cascade={"persist"})
     */
    private $fidele;

  

    /**
     * @ORM\ManyToOne(targetEntity=Cotisation::class, inversedBy="detailcotisations", cascade={"persist"})
     */
    private $cotisation;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="detailcotisations",  cascade={"persist"} )
     */
    private $eglise;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Fidelecotiser::class, inversedBy="detailcotisations", cascade={"persist"})
     */
    private $fidelecotiser;

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

   

    public function getCotisation(): ?Cotisation
    {
        return $this->cotisation;
    }

    public function setCotisation(?Cotisation $cotisation): self
    {
        $this->cotisation = $cotisation;

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

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFidelecotiser(): ?Fidelecotiser
    {
        return $this->fidelecotiser;
    }

    public function setFidelecotiser(?Fidelecotiser $fidelecotiser): self
    {
        $this->fidelecotiser = $fidelecotiser;

        return $this;
    }
}
