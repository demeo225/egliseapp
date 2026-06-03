<?php

namespace App\Entity;

use App\Repository\SeancecelluleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SeancecelluleRepository::class)
 */
class Seancecellule extends SeanceEntity
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
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="seancecellules")
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class)
     */
    private $fidele;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Officiant::class)
     */
    private $officiant;

    /**
     * @ORM\OneToMany(targetEntity=Presencecellule::class, mappedBy="seancecellule")
     */
    private $presencecellules;

    /**
     * @ORM\OneToMany(targetEntity=Invitecellule::class, mappedBy="seancecellule")
     */
    private $invitecellules;

    public function __construct()
    {
        $this->presencecellules = new ArrayCollection();
        $this->invitecellules = new ArrayCollection();
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

    public function getCellule(): ?Cellule
    {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self
    {
        $this->cellule = $cellule;

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

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

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

    /**
     * @return Collection<int, Presencecellule>
     */
    public function getPresencecellules(): Collection
    {
        return $this->presencecellules;
    }

    public function addPresencecellule(Presencecellule $presencecellule): self
    {
        if (!$this->presencecellules->contains($presencecellule)) {
            $this->presencecellules[] = $presencecellule;
            $presencecellule->setSeancecellule($this);
        }

        return $this;
    }

    public function removePresencecellule(Presencecellule $presencecellule): self
    {
        if ($this->presencecellules->removeElement($presencecellule)) {
            // set the owning side to null (unless already changed)
            if ($presencecellule->getSeancecellule() === $this) {
                $presencecellule->setSeancecellule(null);
            }
        }

        return $this;
    }
    
    public function __toString() {
        return $this->getDatesuper()->format('d-m-Y');
    }

    /**
     * @return Collection<int, Invitecellule>
     */
    public function getInvitecellules(): Collection
    {
        return $this->invitecellules;
    }

    public function addInvitecellule(Invitecellule $invitecellule): self
    {
        if (!$this->invitecellules->contains($invitecellule)) {
            $this->invitecellules[] = $invitecellule;
            $invitecellule->setSeancecellule($this);
        }

        return $this;
    }

    public function removeInvitecellule(Invitecellule $invitecellule): self
    {
        if ($this->invitecellules->removeElement($invitecellule)) {
            // set the owning side to null (unless already changed)
            if ($invitecellule->getSeancecellule() === $this) {
                $invitecellule->setSeancecellule(null);
            }
        }

        return $this;
    }
}
