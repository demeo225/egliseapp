<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProjetRepository::class)
 */
class Projet extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ouvrage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $chefprojet;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $devis;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montantdepense;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $periode;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     * @Gedmo\Timestampable(on="update")

     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="projet")
     */
    private $eglise;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $debutprojet;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $finprojet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLibelle(): ?string {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(?string $status): self {
        $this->status = $status;

        return $this;
    }

    public function getOuvrage(): ?string {
        return $this->ouvrage;
    }

    public function setOuvrage(?string $ouvrage): self {
        $this->ouvrage = $ouvrage;

        return $this;
    }

    public function getChefprojet(): ?string {
        return $this->chefprojet;
    }

    public function setChefprojet(?string $chefprojet): self {
        $this->chefprojet = $chefprojet;

        return $this;
    }

    public function getDevis(): ?int {
        return $this->devis;
    }

    public function setDevis(?int $devis): self {
        $this->devis = $devis;

        return $this;
    }

    public function getMontantdepense(): ?int {
        return $this->montantdepense;
    }

    public function setMontantdepense(?int $montantdepense): self {
        $this->montantdepense = $montantdepense;

        return $this;
    }

    public function getPeriode(): ?string {
        return $this->periode;
    }

    public function setPeriode(?string $periode): self {
        $this->periode = $periode;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }

    public function getDebutprojet(): ?\DateTimeInterface
    {
        return $this->debutprojet;
    }

    public function setDebutprojet(?\DateTimeInterface $debutprojet): self
    {
        $this->debutprojet = $debutprojet;

        return $this;
    }

    public function getFinprojet(): ?\DateTimeInterface
    {
        return $this->finprojet;
    }

    public function setFinprojet(?\DateTimeInterface $finprojet): self
    {
        $this->finprojet = $finprojet;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}
