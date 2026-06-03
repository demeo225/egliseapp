<?php

namespace App\Entity;

use App\Repository\FidelecotiserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FidelecotiserRepository::class)
 */
class Fidelecotiser extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecotiser;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montpaye;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $restecotiser;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatcotiser;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="fidelecotisers")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisation::class, inversedBy="fidelecotisers")
     */
    private $cotisation;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="fidelecotiser")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisation::class, mappedBy="fidelecotiser")
     */
    private $detailcotisations;

    public function __construct()
    {
        $this->detailcotisations = new ArrayCollection();
    }

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatecotiser(): ?\DateTimeInterface
    {
        return $this->datecotiser;
    }

    public function setDatecotiser(?\DateTimeInterface $datecotiser): self
    {
        $this->datecotiser = $datecotiser;

        return $this;
    }

    public function getMontpaye(): ?int
    {
        return $this->montpaye;
    }

    public function setMontpaye(?int $montpaye): self
    {
        $this->montpaye = $montpaye;

        return $this;
    }

    public function getRestecotiser(): ?int
    {
        return $this->restecotiser;
    }

    public function setRestecotiser(?int $restecotiser): self
    {
        $this->restecotiser = $restecotiser;

        return $this;
    }

    public function getEtatcotiser(): ?bool
    {
        return $this->etatcotiser;
    }

    public function setEtatcotiser(?bool $etatcotiser): self
    {
        $this->etatcotiser = $etatcotiser;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
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

    public function getCotisation(): ?Cotisation
    {
        return $this->cotisation;
    }

    public function setCotisation(?Cotisation $cotisation): self
    {
        $this->cotisation = $cotisation;

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
     * @return Collection<int, Detailcotisation>
     */
    public function getDetailcotisations(): Collection
    {
        return $this->detailcotisations;
    }

    public function addDetailcotisation(Detailcotisation $detailcotisation): self
    {
        if (!$this->detailcotisations->contains($detailcotisation)) {
            $this->detailcotisations[] = $detailcotisation;
            $detailcotisation->setFidelecotiser($this);
        }

        return $this;
    }

    public function removeDetailcotisation(Detailcotisation $detailcotisation): self
    {
        if ($this->detailcotisations->removeElement($detailcotisation)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisation->getFidelecotiser() === $this) {
                $detailcotisation->setFidelecotiser(null);
            }
        }

        return $this;
    }

}
