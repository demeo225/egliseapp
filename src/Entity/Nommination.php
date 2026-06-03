<?php

namespace App\Entity;

use App\Repository\NomminationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=NomminationRepository::class)
 */
class Nommination extends AbstractEntity
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
    private $datenomination;

 
    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="nomminations")
     */
    private $eglise;
    
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $decret;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $deteil;



    /**
     * @ORM\ManyToMany(targetEntity=Fidele::class, inversedBy="nomminations")
     */
    private $fidele;

    public function __construct()
    {
        $this->fidele = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatenomination(): ?\DateTimeInterface
    {
        return $this->datenomination;
    }

    public function setDatenomination(?\DateTimeInterface $datenomination): self
    {
        $this->datenomination = $datenomination;

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

 
    public function getDecret(): ?string
    {
        return $this->decret;
    }

    public function setDecret(?string $decret): self
    {
        $this->decret = $decret;

        return $this;
    }

    public function __toString() {
        return $this->decret;
    }

    public function getDeteil(): ?string
    {
        return $this->deteil;
    }

    public function setDeteil(?string $deteil): self
    {
        $this->deteil = $deteil;

        return $this;
    }

   
    /**
     * @return Collection<int, Fidele>
     */
    public function getFidele(): Collection
    {
        return $this->fidele;
    }
}
