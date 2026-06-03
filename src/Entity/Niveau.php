<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NiveauRepository::class)
 */
class Niveau extends AbstractEntity 
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
    private $libelle;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updateedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ideglise;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="niveaux")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="niveau")
     */
    private $fideles;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->fideles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

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

    public function getUpdateedAt(): ?\DateTimeInterface
    {
        return $this->updateedAt;
    }

    public function setUpdateedAt(?\DateTimeInterface $updateedAt): self
    {
        $this->updateedAt = $updateedAt;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }

    /**
     * @return Collection<int, Fidele>
     */
    public function getFideles(): Collection
    {
        return $this->fideles;
    }

    public function addFidele(Fidele $fidele): self
    {
        if (!$this->fideles->contains($fidele)) {
            $this->fideles[] = $fidele;
            $fidele->setNiveau($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fideles->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getNiveau() === $this) {
                $fidele->setNiveau(null);
            }
        }

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
}
