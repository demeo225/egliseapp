<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegionRepository::class)
 */
class Region extends AbstractEntity
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Eglise::class, mappedBy="region")
     */
    private $eglises;

    public function __construct()
    {
        $this->eglises = new ArrayCollection();
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Eglise>
     */
    public function getEglises(): Collection
    {
        return $this->eglises;
    }

    public function addEglise(Eglise $eglise): self
    {
        if (!$this->eglises->contains($eglise)) {
            $this->eglises[] = $eglise;
            $eglise->setRegion($this);
        }

        return $this;
    }

    public function removeEglise(Eglise $eglise): self
    {
        if ($this->eglises->removeElement($eglise)) {
            // set the owning side to null (unless already changed)
            if ($eglise->getRegion() === $this) {
                $eglise->setRegion(null);
            }
        }

        return $this;
    }
     public function __toString() {
        return $this->libelle;
    }
}
