<?php

namespace App\Entity;

use App\Repository\CotisationparcelluleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationparcelluleRepository::class)
 */
class Cotisationparcellule extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationparcellules")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserparcellule::class, mappedBy="cotisationparcellule")
     */
    private $cotiserparcellules;

    /**
     * @ORM\OneToMany(targetEntity=Detailparcellule::class, mappedBy="cotisationparcellule")
     */
    private $detailparcellules;

    public function __construct()
    {
        $this->cotiserparcellules = new ArrayCollection();
        $this->detailparcellules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Cotiserparcellule>
     */
    public function getCotiserparcellules(): Collection
    {
        return $this->cotiserparcellules;
    }

    public function addCotiserparcellule(Cotiserparcellule $cotiserparcellule): self
    {
        if (!$this->cotiserparcellules->contains($cotiserparcellule)) {
            $this->cotiserparcellules[] = $cotiserparcellule;
            $cotiserparcellule->setCotisationparcellule($this);
        }

        return $this;
    }

    public function removeCotiserparcellule(Cotiserparcellule $cotiserparcellule): self
    {
        if ($this->cotiserparcellules->removeElement($cotiserparcellule)) {
            // set the owning side to null (unless already changed)
            if ($cotiserparcellule->getCotisationparcellule() === $this) {
                $cotiserparcellule->setCotisationparcellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailparcellule>
     */
    public function getDetailparcellules(): Collection
    {
        return $this->detailparcellules;
    }

    public function addDetailparcellule(Detailparcellule $detailparcellule): self
    {
        if (!$this->detailparcellules->contains($detailparcellule)) {
            $this->detailparcellules[] = $detailparcellule;
            $detailparcellule->setCotisationparcellule($this);
        }

        return $this;
    }

    public function removeDetailparcellule(Detailparcellule $detailparcellule): self
    {
        if ($this->detailparcellules->removeElement($detailparcellule)) {
            // set the owning side to null (unless already changed)
            if ($detailparcellule->getCotisationparcellule() === $this) {
                $detailparcellule->setCotisationparcellule(null);
            }
        }

        return $this;
    }
}
