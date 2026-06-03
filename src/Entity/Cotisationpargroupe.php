<?php

namespace App\Entity;

use App\Repository\CotisationpargroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationpargroupeRepository::class)
 */
class Cotisationpargroupe extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationpargroupes")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpargroupe::class, mappedBy="cotisationpargroupe")
     */
    private $cotiserpargroupes;

    /**
     * @ORM\OneToMany(targetEntity=Detailpargroupe::class, mappedBy="cotisationpargroupe")
     */
    private $detailpargroupes;

    public function __construct()
    {
        $this->cotiserpargroupes = new ArrayCollection();
        $this->detailpargroupes = new ArrayCollection();
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
     * @return Collection<int, Cotiserpargroupe>
     */
    public function getCotiserpargroupes(): Collection
    {
        return $this->cotiserpargroupes;
    }

    public function addCotiserpargroupe(Cotiserpargroupe $cotiserpargroupe): self
    {
        if (!$this->cotiserpargroupes->contains($cotiserpargroupe)) {
            $this->cotiserpargroupes[] = $cotiserpargroupe;
            $cotiserpargroupe->setCotisationpargroupe($this);
        }

        return $this;
    }

    public function removeCotiserpargroupe(Cotiserpargroupe $cotiserpargroupe): self
    {
        if ($this->cotiserpargroupes->removeElement($cotiserpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpargroupe->getCotisationpargroupe() === $this) {
                $cotiserpargroupe->setCotisationpargroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailpargroupe>
     */
    public function getDetailpargroupes(): Collection
    {
        return $this->detailpargroupes;
    }

    public function addDetailpargroupe(Detailpargroupe $detailpargroupe): self
    {
        if (!$this->detailpargroupes->contains($detailpargroupe)) {
            $this->detailpargroupes[] = $detailpargroupe;
            $detailpargroupe->setCotisationpargroupe($this);
        }

        return $this;
    }

    public function removeDetailpargroupe(Detailpargroupe $detailpargroupe): self
    {
        if ($this->detailpargroupes->removeElement($detailpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($detailpargroupe->getCotisationpargroupe() === $this) {
                $detailpargroupe->setCotisationpargroupe(null);
            }
        }

        return $this;
    }
}
