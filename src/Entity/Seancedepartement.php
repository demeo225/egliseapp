<?php

namespace App\Entity;

use App\Repository\SeancedepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeancedepartementRepository::class)
 */
class Seancedepartement extends SeanceEntity
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
     * @ORM\ManyToOne(targetEntity=Officiant::class)
     */
    private $officiant;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class)
     */
    private $departement;

    /**
     * @ORM\OneToMany(targetEntity=Presencedepartement::class, mappedBy="seancedepartement")
     */
    private $presencedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Invitedepartement::class, mappedBy="seancedepartement")
     */
    private $invitedepartements;

    public function __construct()
    {
        $this->presencedepartements = new ArrayCollection();
        $this->invitedepartements = new ArrayCollection();
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

    public function getOfficiant(): ?Officiant
    {
        return $this->officiant;
    }

    public function setOfficiant(?Officiant $officiant): self
    {
        $this->officiant = $officiant;

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

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }
    
    public function __toString() {
        return $this->getDatesuper()->format('d-m-Y');
    }

    /**
     * @return Collection<int, Presencedepartement>
     */
    public function getPresencedepartements(): Collection
    {
        return $this->presencedepartements;
    }

    public function addPresencedepartement(Presencedepartement $presencedepartement): self
    {
        if (!$this->presencedepartements->contains($presencedepartement)) {
            $this->presencedepartements[] = $presencedepartement;
            $presencedepartement->setSeancedepartement($this);
        }

        return $this;
    }

    public function removePresencedepartement(Presencedepartement $presencedepartement): self
    {
        if ($this->presencedepartements->removeElement($presencedepartement)) {
            // set the owning side to null (unless already changed)
            if ($presencedepartement->getSeancedepartement() === $this) {
                $presencedepartement->setSeancedepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitedepartement>
     */
    public function getInvitedepartements(): Collection
    {
        return $this->invitedepartements;
    }

    public function addInvitedepartement(Invitedepartement $invitedepartement): self
    {
        if (!$this->invitedepartements->contains($invitedepartement)) {
            $this->invitedepartements[] = $invitedepartement;
            $invitedepartement->setSeancedepartement($this);
        }

        return $this;
    }

    public function removeInvitedepartement(Invitedepartement $invitedepartement): self
    {
        if ($this->invitedepartements->removeElement($invitedepartement)) {
            // set the owning side to null (unless already changed)
            if ($invitedepartement->getSeancedepartement() === $this) {
                $invitedepartement->setSeancedepartement(null);
            }
        }

        return $this;
    }
}
