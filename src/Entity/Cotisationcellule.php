<?php

namespace App\Entity;

use App\Repository\CotisationcelluleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationcelluleRepository::class)
 */
class Cotisationcellule extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="cotisationcellules")
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationcellules")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Cotisercellule::class, mappedBy="cotisationcellule")
     */
    private $cotisercellules;

    public function __construct()
    {
        $this->cotisercellules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCellule(): ?Cellule
    {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self
    {
        $this->cellule = $cellule;

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
     * @return Collection<int, Cotisercellule>
     */
    public function getCotisercellules(): Collection
    {
        return $this->cotisercellules;
    }

    public function addCotisercellule(Cotisercellule $cotisercellule): self
    {
        if (!$this->cotisercellules->contains($cotisercellule)) {
            $this->cotisercellules[] = $cotisercellule;
            $cotisercellule->setCotisationcellule($this);
        }

        return $this;
    }

    public function removeCotisercellule(Cotisercellule $cotisercellule): self
    {
        if ($this->cotisercellules->removeElement($cotisercellule)) {
            // set the owning side to null (unless already changed)
            if ($cotisercellule->getCotisationcellule() === $this) {
                $cotisercellule->setCotisationcellule(null);
            }
        }

        return $this;
    }
    
    public function __toString() {
        return $this->getObjet();
    }
}
