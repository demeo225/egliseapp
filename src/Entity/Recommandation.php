<?php

namespace App\Entity;

use App\Repository\RecommandationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RecommandationRepository::class)
 */
class Recommandation extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $destination;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $reference;

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
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="recommandations")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="recommandation")
     */
    private $eglise;


    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $fidelite;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $soummission;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pasteur;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateop;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $stabilite;
    
    
 

    public function getId(): ?int {
        return $this->id;
    }

    public function getDestination(): ?string {
        return $this->destination;
    }

    public function setDestination(string $destination): self {
        $this->destination = $destination;

        return $this;
    }

    public function getObjet(): ?string {
        return $this->objet;
    }

    public function setObjet(?string $objet): self {
        $this->objet = $objet;

        return $this;
    }

    public function getReference(): ?string {
        return $this->reference;
    }

    public function setReference(?string $reference): self {
        $this->reference = $reference;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }

   

    public function getFidelite(): ?string
    {
        return $this->fidelite;
    }

    public function setFidelite(?string $fidelite): self
    {
        $this->fidelite = $fidelite;

        return $this;
    }

    public function getSoummission(): ?string
    {
        return $this->soummission;
    }

    public function setSoummission(?string $soummission): self
    {
        $this->soummission = $soummission;

        return $this;
    }

    public function getPasteur(): ?string
    {
        return $this->pasteur;
    }

    public function setPasteur(?string $pasteur): self
    {
        $this->pasteur = $pasteur;

        return $this;
    }

    public function getDateop(): ?\DateTimeInterface
    {
        return $this->dateop;
    }

    public function setDateop(?\DateTimeInterface $dateop): self
    {
        $this->dateop = $dateop;

        return $this;
    }

    public function getStabilite(): ?string
    {
        return $this->stabilite;
    }

    public function setStabilite(?string $stabilite): self
    {
        $this->stabilite = $stabilite;

        return $this;
    }

}
