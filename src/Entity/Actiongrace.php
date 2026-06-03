<?php

namespace App\Entity;

use App\Repository\ActiongraceRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActiongraceRepository::class)
 */
class Actiongrace extends AbstractEntity {

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="actiongraces")
     */
    private $fidele;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateactiongrace;

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
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    public function getId(): ?int {
        return $this->id;
    }

    public function getMontant(): ?int {
        return $this->montant;
    }

    public function setMontant(?int $montant): self {
        $this->montant = $montant;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): self {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getFidele(): ?Fidele {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self {
        $this->fidele = $fidele;

        return $this;
    }

    public function getDateactiongrace(): ?\DateTimeInterface {
        return $this->dateactiongrace;
    }

    public function setDateactiongrace(?\DateTimeInterface $dateactiongrace): self {
        $this->dateactiongrace = $dateactiongrace;

        return $this;
    }

    public function getDixmille(): ?int {
        return $this->dixmille;
    }

    public function setDixmille(?int $dixmille): self {
        $this->dixmille = $dixmille;

        return $this;
    }

    public function getCinqmille(): ?int {
        return $this->cinqmille;
    }

    public function setCinqmille(?int $cinqmille): self {
        $this->cinqmille = $cinqmille;

        return $this;
    }

    public function getDeuxmille(): ?int {
        return $this->deuxmille;
    }

    public function setDeuxmille(?int $deuxmille): self {
        $this->deuxmille = $deuxmille;

        return $this;
    }

    public function getMille(): ?int {
        return $this->mille;
    }

    public function setMille(?int $mille): self {
        $this->mille = $mille;

        return $this;
    }

    public function getCentbillet(): ?int {
        return $this->centbillet;
    }

    public function setCentbillet(?int $centbillet): self {
        $this->centbillet = $centbillet;

        return $this;
    }

    public function getCentpiece(): ?int {
        return $this->centpiece;
    }

    public function setCentpiece(?int $centpiece): self {
        $this->centpiece = $centpiece;

        return $this;
    }

    public function getDeuxcent(): ?int {
        return $this->deuxcent;
    }

    public function setDeuxcent(?int $deuxcent): self {
        $this->deuxcent = $deuxcent;

        return $this;
    }

    public function getCent(): ?int {
        return $this->cent;
    }

    public function setCent(?int $cent): self {
        $this->cent = $cent;

        return $this;
    }

    public function getCinquante(): ?int {
        return $this->cinquante;
    }

    public function setCinquante(?int $cinquante): self {
        $this->cinquante = $cinquante;

        return $this;
    }

    public function getVingtcinq(): ?int {
        return $this->vingtcinq;
    }

    public function setVingtcinq(?int $vingtcinq): self {
        $this->vingtcinq = $vingtcinq;

        return $this;
    }

    public function getDix(): ?int {
        return $this->dix;
    }

    public function setDix(int $dix): self {
        $this->dix = $dix;

        return $this;
    }

    public function getCinq(): ?int {
        return $this->cinq;
    }

    public function setCinq(?int $cinq): self {
        $this->cinq = $cinq;

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
