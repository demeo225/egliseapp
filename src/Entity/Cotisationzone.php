<?php

namespace App\Entity;

use App\Repository\CotisationzoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationzoneRepository::class)
 */
class Cotisationzone extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="cotisationzones")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationzones")
     */
    private $eglise;

  

    /**
     * @ORM\OneToMany(targetEntity=Cotiserzone::class, mappedBy="cotisationzone")
     */
    private $cotiserzones;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationzone::class, mappedBy="cotisationzone")
     */
    private $detailcotisationzones;


    public function __construct()
    {
        $this->cotiserzones = new ArrayCollection();
        $this->detailcotisationzones = new ArrayCollection();
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
 
     
    public function __toString() {
        return $this->getObjet();
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
            $cotiserzone->setCotisationzone($this);
        }

        return $this;
    }

    public function removeCotiserzone(Cotiserzone $cotiserzone): self
    {
        if ($this->cotiserzones->removeElement($cotiserzone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserzone->getCotisationzone() === $this) {
                $cotiserzone->setCotisationzone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisationzone>
     */
    public function getDetailcotisationzones(): Collection
    {
        return $this->detailcotisationzones;
    }

    public function addDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self
    {
        if (!$this->detailcotisationzones->contains($detailcotisationzone)) {
            $this->detailcotisationzones[] = $detailcotisationzone;
            $detailcotisationzone->setCotisationzone($this);
        }

        return $this;
    }

    public function removeDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self
    {
        if ($this->detailcotisationzones->removeElement($detailcotisationzone)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationzone->getCotisationzone() === $this) {
                $detailcotisationzone->setCotisationzone(null);
            }
        }

        return $this;
    }

  
}
