<?php

namespace App\Entity;

use App\Repository\DetailcotisationecodimRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DetailcotisationecodimRepository::class)
 */
class Detailcotisationecodim extends AbstractEntity
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
     * @ORM\Column(type="integer")
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
     * @ORM\ManyToOne(targetEntity=Enfant::class)
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity=Ecodimactivite::class)
     */
    private $ecodimactivite;

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

    public function setMontant(int $montant): self
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

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }

    public function getEcodimactivite(): ?Ecodimactivite
    {
        return $this->ecodimactivite;
    }

    public function setEcodimactivite(?Ecodimactivite $ecodimactivite): self
    {
        $this->ecodimactivite = $ecodimactivite;

        return $this;
    }
}
