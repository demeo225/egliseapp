<?php

namespace App\Entity;

use App\Repository\ZonecotisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZonecotisationRepository::class)
 */
class Zonecotisation extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="zonecotisations")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="zonecotisations")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserzone::class, mappedBy="zonecotisation")
     */
    private $cotiserzones;

    public function __construct()
    {
        $this->cotiserzones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

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
     * @return Collection<int, Cotiserzone>
     */
    public function getCotiserzones(): Collection
    {
        return $this->cotiserzones;
    }

    public function addCotiserzone(Cotiserzone $cotiserzone): self
    {
        if (!$this->cotiserzones->contains($cotiserzone)) {
            $this->cotiserzones[] = $cotiserzone;
            $cotiserzone->setZonecotisation($this);
        }

        return $this;
    }

    public function removeCotiserzone(Cotiserzone $cotiserzone): self
    {
        if ($this->cotiserzones->removeElement($cotiserzone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserzone->getZonecotisation() === $this) {
                $cotiserzone->setZonecotisation(null);
            }
        }

        return $this;
    }
    
    public function __toString() {
        return $this->getObjet();
    }
}
