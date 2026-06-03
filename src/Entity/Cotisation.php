<?php

namespace App\Entity;

use App\Repository\CotisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CotisationRepository::class)
 */
class Cotisation extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objet;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatcotisation;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisation")
     */
    private $eglise;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $typecotisation;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $periodecotisation;

    /**
     * @ORM\OneToMany(targetEntity=Fidelecotiser::class, mappedBy="cotisation")
     */
    private $fidelecotisers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisation::class, mappedBy="cotisation")
     */
    private $detailcotisations;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpazone::class, mappedBy="cotisationparzone")
     */
    private $cotiserpazones;

    public function __construct()
    {
        $this->fidelecotisers = new ArrayCollection();
        $this->detailcotisations = new ArrayCollection();
        $this->cotiserpazones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(?string $objet): self
    {
        $this->objet = $objet;

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


    public function getEtatcotisation(): ?bool
    {
        return $this->etatcotisation;
    }

    public function setEtatcotisation(?bool $etatcotisation): self
    {
        $this->etatcotisation = $etatcotisation;

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

    public function getTypecotisation(): ?string
    {
        return $this->typecotisation;
    }

    public function setTypecotisation(?string $typecotisation): self
    {
        $this->typecotisation = $typecotisation;

        return $this;
    }

    public function getPeriodecotisation(): ?\DateTimeInterface
    {
        return $this->periodecotisation;
    }

    public function setPeriodecotisation(?\DateTimeInterface $periodecotisation): self
    {
        $this->periodecotisation = $periodecotisation;

        return $this;
    }

    /**
     * @return Collection|Fidelecotiser[]
     */
    public function getFidelecotisers(): Collection
    {
        return $this->fidelecotisers;
    }

    public function addFidelecotiser(Fidelecotiser $fidelecotiser): self
    {
        if (!$this->fidelecotisers->contains($fidelecotiser)) {
            $this->fidelecotisers[] = $fidelecotiser;
            $fidelecotiser->setCotisation($this);
        }

        return $this;
    }

    public function removeFidelecotiser(Fidelecotiser $fidelecotiser): self
    {
        if ($this->fidelecotisers->removeElement($fidelecotiser)) {
            // set the owning side to null (unless already changed)
            if ($fidelecotiser->getCotisation() === $this) {
                $fidelecotiser->setCotisation(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): self
    {
        $this->total = $total;

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
            $detailcotisation->setCotisation($this);
        }

        return $this;
    }

    public function removeDetailcotisation(Detailcotisation $detailcotisation): self
    {
        if ($this->detailcotisations->removeElement($detailcotisation)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisation->getCotisation() === $this) {
                $detailcotisation->setCotisation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserpazone>
     */
    public function getCotiserpazones(): Collection
    {
        return $this->cotiserpazones;
    }

    public function addCotiserpazone(Cotiserpazone $cotiserpazone): self
    {
        if (!$this->cotiserpazones->contains($cotiserpazone)) {
            $this->cotiserpazones[] = $cotiserpazone;
            $cotiserpazone->setCotisationparzone($this);
        }

        return $this;
    }

    public function removeCotiserpazone(Cotiserpazone $cotiserpazone): self
    {
        if ($this->cotiserpazones->removeElement($cotiserpazone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpazone->getCotisationparzone() === $this) {
                $cotiserpazone->setCotisationparzone(null);
            }
        }

        return $this;
    }
}
