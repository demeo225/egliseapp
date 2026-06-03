<?php

namespace App\Entity;

use App\Repository\CotisationdepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationdepartementRepository::class)
 */
class Cotisationdepartement extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

   
    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="cotisationdepartements")
     */
    private $departement;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserdepartement::class, mappedBy="cotisationdepartement")
     */
    private $cotiserdepartements;

    public function __construct()
    {
        $this->cotiserdepartements = new ArrayCollection();
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
    public function __toString() {
        return $this->getObjet();
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * @return Collection<int, Cotiserdepartement>
     */
    public function getCotiserdepartements(): Collection
    {
        return $this->cotiserdepartements;
    }

    public function addCotiserdepartement(Cotiserdepartement $cotiserdepartement): self
    {
        if (!$this->cotiserdepartements->contains($cotiserdepartement)) {
            $this->cotiserdepartements[] = $cotiserdepartement;
            $cotiserdepartement->setCotisationdepartement($this);
        }

        return $this;
    }

    public function removeCotiserdepartement(Cotiserdepartement $cotiserdepartement): self
    {
        if ($this->cotiserdepartements->removeElement($cotiserdepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotiserdepartement->getCotisationdepartement() === $this) {
                $cotiserdepartement->setCotisationdepartement(null);
            }
        }

        return $this;
    }

  
}
