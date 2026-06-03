<?php

namespace App\Entity;

use App\Repository\CotisationgroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationgroupeRepository::class)
 */
class Cotisationgroupe extends Cotiser
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
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="cotisationgroupes")
     */
    private $groupe;

    /**
     * @ORM\OneToMany(targetEntity=Cotisergroupe::class, mappedBy="cotisationgroupe")
     */
    private $cotisergroupes;

    public function __construct()
    {
        $this->cotisergroupes = new ArrayCollection();
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
    


    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
    
    
    public function __toString() {
        return $this->getObjet();
    }

    /**
     * @return Collection<int, Cotisergroupe>
     */
    public function getCotisergroupes(): Collection
    {
        return $this->cotisergroupes;
    }

    public function addCotisergroupe(Cotisergroupe $cotisergroupe): self
    {
        if (!$this->cotisergroupes->contains($cotisergroupe)) {
            $this->cotisergroupes[] = $cotisergroupe;
            $cotisergroupe->setCotisationgroupe($this);
        }

        return $this;
    }

    public function removeCotisergroupe(Cotisergroupe $cotisergroupe): self
    {
        if ($this->cotisergroupes->removeElement($cotisergroupe)) {
            // set the owning side to null (unless already changed)
            if ($cotisergroupe->getCotisationgroupe() === $this) {
                $cotisergroupe->setCotisationgroupe(null);
            }
        }

        return $this;
    }
    

}
   
    

