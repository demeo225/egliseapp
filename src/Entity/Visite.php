<?php

namespace App\Entity;

use App\Repository\VisiteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=VisiteRepository::class)
 */
class Visite extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $visiteur;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $contact1;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datevisite;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

     /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;


    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="visites")
     */
    private $receptionpar;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="visites")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisiteur(): ?string
    {
        return $this->visiteur;
    }

    public function setVisiteur(?string $visiteur): self
    {
        $this->visiteur = $visiteur;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getContact1(): ?string
    {
        return $this->contact1;
    }

    public function setContact1(?string $contact1): self
    {
        $this->contact1 = $contact1;

        return $this;
    }

    public function getDatevisite(): ?\DateTimeInterface
    {
        return $this->datevisite;
    }

    public function setDatevisite(?\DateTimeInterface $datevisite): self
    {
        $this->datevisite = $datevisite;

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

    public function getReceptionpar(): ?Fidele
    {
        return $this->receptionpar;
    }

    public function setReceptionpar(?Fidele $receptionpar): self
    {
        $this->receptionpar = $receptionpar;

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
