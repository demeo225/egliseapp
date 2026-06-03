<?php

namespace App\Entity;

use App\Repository\CotisationparzoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationparzoneRepository::class)
 */
class Cotisationparzone extends Cotiser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationparzones")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Detailparzone::class, mappedBy="cotisationparzone")
     */
    private $detailparzones;

    public function __construct()
    {
        $this->detailparzones = new ArrayCollection();
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
     * @return Collection<int, Detailparzone>
     */
    public function getDetailparzones(): Collection
    {
        return $this->detailparzones;
    }

    public function addDetailparzone(Detailparzone $detailparzone): self
    {
        if (!$this->detailparzones->contains($detailparzone)) {
            $this->detailparzones[] = $detailparzone;
            $detailparzone->setCotisationparzone($this);
        }

        return $this;
    }

    public function removeDetailparzone(Detailparzone $detailparzone): self
    {
        if ($this->detailparzones->removeElement($detailparzone)) {
            // set the owning side to null (unless already changed)
            if ($detailparzone->getCotisationparzone() === $this) {
                $detailparzone->setCotisationparzone(null);
            }
        }

        return $this;
    }
}
