<?php

namespace App\Entity;

use App\Repository\CotisationfamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationfamilleRepository::class)
 */
class Cotisationfamille extends Cotiser
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
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="cotisationfamilles")
     */
    private $famille;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserfamille::class, mappedBy="cotisationfamille")
     */
    private $cotiserfamilles;

    public function __construct()
    {
        $this->cotiserfamilles = new ArrayCollection();
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

    public function getFamille(): ?Famille
    {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * @return Collection<int, Cotiserfamille>
     */
    public function getCotiserfamilles(): Collection
    {
        return $this->cotiserfamilles;
    }

    public function addCotiserfamille(Cotiserfamille $cotiserfamille): self
    {
        if (!$this->cotiserfamilles->contains($cotiserfamille)) {
            $this->cotiserfamilles[] = $cotiserfamille;
            $cotiserfamille->setCotisationfamille($this);
        }

        return $this;
    }

    public function removeCotiserfamille(Cotiserfamille $cotiserfamille): self
    {
        if ($this->cotiserfamilles->removeElement($cotiserfamille)) {
            // set the owning side to null (unless already changed)
            if ($cotiserfamille->getCotisationfamille() === $this) {
                $cotiserfamille->setCotisationfamille(null);
            }
        }

        return $this;
    }
}
