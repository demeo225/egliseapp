<?php

namespace App\Entity;

use App\Repository\SeancegroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeancegroupeRepository::class)
 */
class Seancegroupe extends SeanceEntity
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
     * @ORM\ManyToOne(targetEntity=Groupe::class)
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity=Officiant::class)
     */
    private $officiant;

    /**
     * @ORM\OneToMany(targetEntity=Presencegroupe::class, mappedBy="seancegroupe")
     */
    private $presencegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Invitegroupe::class, mappedBy="seancegroupe")
     */
    private $invitegroupes;

    public function __construct()
    {
        $this->presencegroupes = new ArrayCollection();
        $this->invitegroupes = new ArrayCollection();
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

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

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
     * @return Collection<int, Presencegroupe>
     */
    public function getPresencegroupes(): Collection
    {
        return $this->presencegroupes;
    }

    public function addPresencegroupe(Presencegroupe $presencegroupe): self
    {
        if (!$this->presencegroupes->contains($presencegroupe)) {
            $this->presencegroupes[] = $presencegroupe;
            $presencegroupe->setSeancegroupe($this);
        }

        return $this;
    }

    public function removePresencegroupe(Presencegroupe $presencegroupe): self
    {
        if ($this->presencegroupes->removeElement($presencegroupe)) {
            // set the owning side to null (unless already changed)
            if ($presencegroupe->getSeancegroupe() === $this) {
                $presencegroupe->setSeancegroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitegroupe>
     */
    public function getInvitegroupes(): Collection
    {
        return $this->invitegroupes;
    }

    public function addInvitegroupe(Invitegroupe $invitegroupe): self
    {
        if (!$this->invitegroupes->contains($invitegroupe)) {
            $this->invitegroupes[] = $invitegroupe;
            $invitegroupe->setSeancegroupe($this);
        }

        return $this;
    }

    public function removeInvitegroupe(Invitegroupe $invitegroupe): self
    {
        if ($this->invitegroupes->removeElement($invitegroupe)) {
            // set the owning side to null (unless already changed)
            if ($invitegroupe->getSeancegroupe() === $this) {
                $invitegroupe->setSeancegroupe(null);
            }
        }

        return $this;
    }
}
