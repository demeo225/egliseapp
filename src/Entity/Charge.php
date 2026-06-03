<?php

namespace App\Entity;

use App\Repository\ChargeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChargeRepository::class)
 */
class Charge extends AbstractEntity
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
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detail;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecharge;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="charges")
     */
    private $eglise;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ideglise;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="charges")
     */
    private $departement;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dixmille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cinqmille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deuxmille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $centbillet;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $centpiece;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deuxcent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cinquante;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vingtcinq;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dix;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cinq;

    /**
     * @ORM\ManyToOne(targetEntity=Objetcharge::class, inversedBy="charges")
     */
    private $objetcharge;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ajout;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getDatecharge(): ?\DateTimeInterface
    {
        return $this->datecharge;
    }

    public function setDatecharge(?\DateTimeInterface $datecharge): self
    {
        $this->datecharge = $datecharge;

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

    public function getIdeglise(): ?int
    {
        return $this->ideglise;
    }

    public function setIdeglise(?int $ideglise): self
    {
        $this->ideglise = $ideglise;

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDixmille(): ?int
    {
        return $this->dixmille;
    }

    public function setDixmille(?int $dixmille): self
    {
        $this->dixmille = $dixmille;

        return $this;
    }

    public function getCinqmille(): ?int
    {
        return $this->cinqmille;
    }

    public function setCinqmille(?int $cinqmille): self
    {
        $this->cinqmille = $cinqmille;

        return $this;
    }

    public function getDeuxmille(): ?int
    {
        return $this->deuxmille;
    }

    public function setDeuxmille(?int $deuxmille): self
    {
        $this->deuxmille = $deuxmille;

        return $this;
    }

    public function getMille(): ?int
    {
        return $this->mille;
    }

    public function setMille(?int $mille): self
    {
        $this->mille = $mille;

        return $this;
    }

    public function getCentbillet(): ?int
    {
        return $this->centbillet;
    }

    public function setCentbillet(?int $centbillet): self
    {
        $this->centbillet = $centbillet;

        return $this;
    }

    public function getCentpiece(): ?int
    {
        return $this->centpiece;
    }

    public function setCentpiece(?int $centpiece): self
    {
        $this->centpiece = $centpiece;

        return $this;
    }

    public function getDeuxcent(): ?int
    {
        return $this->deuxcent;
    }

    public function setDeuxcent(?int $deuxcent): self
    {
        $this->deuxcent = $deuxcent;

        return $this;
    }

    public function getCent(): ?int
    {
        return $this->cent;
    }

    public function setCent(?int $cent): self
    {
        $this->cent = $cent;

        return $this;
    }

    public function getCinquante(): ?int
    {
        return $this->cinquante;
    }

    public function setCinquante(?int $cinquante): self
    {
        $this->cinquante = $cinquante;

        return $this;
    }

    public function getVingtcinq(): ?int
    {
        return $this->vingtcinq;
    }

    public function setVingtcinq(?int $vingtcinq): self
    {
        $this->vingtcinq = $vingtcinq;

        return $this;
    }

    public function getDix(): ?int
    {
        return $this->dix;
    }

    public function setDix(?int $dix): self
    {
        $this->dix = $dix;

        return $this;
    }

    public function getCinq(): ?int
    {
        return $this->cinq;
    }

    public function setCinq(?int $cinq): self
    {
        $this->cinq = $cinq;

        return $this;
    }

    public function getObjetcharge(): ?Objetcharge
    {
        return $this->objetcharge;
    }

    public function setObjetcharge(?Objetcharge $objetcharge): self
    {
        $this->objetcharge = $objetcharge;

        return $this;
    }

    public function getAjout(): ?int
    {
        return $this->ajout;
    }

    public function setAjout(?int $ajout): self
    {
        $this->ajout = $ajout;

        return $this;
    }
}
