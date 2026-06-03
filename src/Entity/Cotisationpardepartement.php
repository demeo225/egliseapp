<?php

namespace App\Entity;

use App\Repository\CotisationpardepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationpardepartementRepository::class)
 */
class Cotisationpardepartement extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationpardepartements")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpardepartement::class, mappedBy="cotisationpardepartement")
     */
    private $cotiserpardepartements;

    /**
     * @ORM\OneToMany(targetEntity=Detailpardepartement::class, mappedBy="cotisationpardepartement")
     */
    private $detailpardepartements;

    public function __construct()
    {
        $this->cotiserpardepartements = new ArrayCollection();
        $this->detailpardepartements = new ArrayCollection();
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
     * @return Collection<int, Cotiserpardepartement>
     */
    public function getCotiserpardepartements(): Collection
    {
        return $this->cotiserpardepartements;
    }

    public function addCotiserpardepartement(Cotiserpardepartement $cotiserpardepartement): self
    {
        if (!$this->cotiserpardepartements->contains($cotiserpardepartement)) {
            $this->cotiserpardepartements[] = $cotiserpardepartement;
            $cotiserpardepartement->setCotisationpardepartement($this);
        }

        return $this;
    }

    public function removeCotiserpardepartement(Cotiserpardepartement $cotiserpardepartement): self
    {
        if ($this->cotiserpardepartements->removeElement($cotiserpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpardepartement->getCotisationpardepartement() === $this) {
                $cotiserpardepartement->setCotisationpardepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailpardepartement>
     */
    public function getDetailpardepartements(): Collection
    {
        return $this->detailpardepartements;
    }

    public function addDetailpardepartement(Detailpardepartement $detailpardepartement): self
    {
        if (!$this->detailpardepartements->contains($detailpardepartement)) {
            $this->detailpardepartements[] = $detailpardepartement;
            $detailpardepartement->setCotisationpardepartement($this);
        }

        return $this;
    }

    public function removeDetailpardepartement(Detailpardepartement $detailpardepartement): self
    {
        if ($this->detailpardepartements->removeElement($detailpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($detailpardepartement->getCotisationpardepartement() === $this) {
                $detailpardepartement->setCotisationpardepartement(null);
            }
        }

        return $this;
    }
}
