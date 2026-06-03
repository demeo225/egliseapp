<?php

namespace App\Entity;

use App\Repository\CotisationparfamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationparfamilleRepository::class)
 */
class Cotisationparfamille extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationparfamilles")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserparfamille::class, mappedBy="cotisationparfamille")
     */
    private $cotiserparfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Detailparfamille::class, mappedBy="cotisationparfamille")
     */
    private $detailparfamilles;

    public function __construct()
    {
        $this->cotiserparfamilles = new ArrayCollection();
        $this->detailparfamilles = new ArrayCollection();
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
     * @return Collection<int, Cotiserparfamille>
     */
    public function getCotiserparfamilles(): Collection
    {
        return $this->cotiserparfamilles;
    }

    public function addCotiserparfamille(Cotiserparfamille $cotiserparfamille): self
    {
        if (!$this->cotiserparfamilles->contains($cotiserparfamille)) {
            $this->cotiserparfamilles[] = $cotiserparfamille;
            $cotiserparfamille->setCotisationparfamille($this);
        }

        return $this;
    }

    public function removeCotiserparfamille(Cotiserparfamille $cotiserparfamille): self
    {
        if ($this->cotiserparfamilles->removeElement($cotiserparfamille)) {
            // set the owning side to null (unless already changed)
            if ($cotiserparfamille->getCotisationparfamille() === $this) {
                $cotiserparfamille->setCotisationparfamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailparfamille>
     */
    public function getDetailparfamilles(): Collection
    {
        return $this->detailparfamilles;
    }

    public function addDetailparfamille(Detailparfamille $detailparfamille): self
    {
        if (!$this->detailparfamilles->contains($detailparfamille)) {
            $this->detailparfamilles[] = $detailparfamille;
            $detailparfamille->setCotisationparfamille($this);
        }

        return $this;
    }

    public function removeDetailparfamille(Detailparfamille $detailparfamille): self
    {
        if ($this->detailparfamilles->removeElement($detailparfamille)) {
            // set the owning side to null (unless already changed)
            if ($detailparfamille->getCotisationparfamille() === $this) {
                $detailparfamille->setCotisationparfamille(null);
            }
        }

        return $this;
    }
}
