<?php

namespace App\Entity;

use App\Repository\SeancefamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeancefamilleRepository::class)
 */
class Seancefamille extends SeanceEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class)
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

   

    /**
     * @ORM\ManyToOne(targetEntity=Officiant::class)
     */
    private $officiant;

    /**
     * @ORM\OneToMany(targetEntity=Presencefamille::class, mappedBy="seancefamille")
     */
    private $presencefamilles;

    /**
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="seancefamilles")
     */
    private $famille;

    /**
     * @ORM\OneToMany(targetEntity=Invitefamille::class, mappedBy="seancefamille")
     */
    private $invitefamilles;

    public function __construct()
    {
        $this->presencefamilles = new ArrayCollection();
        $this->invitefamilles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFidele(): ?Fidele
    {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self
    {
        $this->fidele = $fidele;

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



    public function getOfficiant(): ?Officiant
    {
        return $this->officiant;
    }

    public function setOfficiant(?Officiant $officiant): self
    {
        $this->officiant = $officiant;

        return $this;
    }
    
    public function __toString() {
        return $this->getDatesuper()->format('d-m-Y');
    }

    /**
     * @return Collection<int, Presencefamille>
     */
    public function getPresencefamilles(): Collection
    {
        return $this->presencefamilles;
    }

    public function addPresencefamille(Presencefamille $presencefamille): self
    {
        if (!$this->presencefamilles->contains($presencefamille)) {
            $this->presencefamilles[] = $presencefamille;
            $presencefamille->setSenacefamille($this);
        }

        return $this;
    }

    public function removePresencefamille(Presencefamille $presencefamille): self
    {
        if ($this->presencefamilles->removeElement($presencefamille)) {
            // set the owning side to null (unless already changed)
            if ($presencefamille->getSenacefamille() === $this) {
                $presencefamille->setSenacefamille(null);
            }
        }

        return $this;
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
     * @return Collection<int, Invitefamille>
     */
    public function getInvitefamilles(): Collection
    {
        return $this->invitefamilles;
    }

    public function addInvitefamille(Invitefamille $invitefamille): self
    {
        if (!$this->invitefamilles->contains($invitefamille)) {
            $this->invitefamilles[] = $invitefamille;
            $invitefamille->setSeancefamille($this);
        }

        return $this;
    }

    public function removeInvitefamille(Invitefamille $invitefamille): self
    {
        if ($this->invitefamilles->removeElement($invitefamille)) {
            // set the owning side to null (unless already changed)
            if ($invitefamille->getSeancefamille() === $this) {
                $invitefamille->setSeancefamille(null);
            }
        }

        return $this;
    }
}
