<?php

namespace App\Entity;

use App\Repository\CotisationsocialeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass=CotisationsocialeRepository::class)
 */
class Cotisationsociale extends AbstractEntity
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
     *  @Assert\NotBlank()
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     *  @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotisationsociales")
     */
    private $eglise;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     *  @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="date", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Detailsociale::class, mappedBy="cotisationsociale")
     */
    private $detailsociales;

    /**
     * @ORM\OneToMany(targetEntity=Cotisersociale::class, mappedBy="cotisationsociale")
     */
    private $cotisersociales;



    public function __construct()
    {
        $this->detailsociales = new ArrayCollection();
        $this->cotisersociales = new ArrayCollection();
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

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function __toString() {
        return $this->getMontant();
    }

    /**
     * @return Collection<int, Detailsociale>
     */
    public function getDetailsociales(): Collection
    {
        return $this->detailsociales;
    }

    public function addDetailsociale(Detailsociale $detailsociale): self
    {
        if (!$this->detailsociales->contains($detailsociale)) {
            $this->detailsociales[] = $detailsociale;
            $detailsociale->setCotisationsociale($this);
        }

        return $this;
    }

    public function removeDetailsociale(Detailsociale $detailsociale): self
    {
        if ($this->detailsociales->removeElement($detailsociale)) {
            // set the owning side to null (unless already changed)
            if ($detailsociale->getCotisationsociale() === $this) {
                $detailsociale->setCotisationsociale(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisersociale>
     */
    public function getCotisersociales(): Collection
    {
        return $this->cotisersociales;
    }

    public function addCotisersociale(Cotisersociale $cotisersociale): self
    {
        if (!$this->cotisersociales->contains($cotisersociale)) {
            $this->cotisersociales[] = $cotisersociale;
            $cotisersociale->setCotisationsociale($this);
        }

        return $this;
    }

    public function removeCotisersociale(Cotisersociale $cotisersociale): self
    {
        if ($this->cotisersociales->removeElement($cotisersociale)) {
            // set the owning side to null (unless already changed)
            if ($cotisersociale->getCotisationsociale() === $this) {
                $cotisersociale->setCotisationsociale(null);
            }
        }

        return $this;
    }


}
