<?php

namespace App\Entity;

use App\Repository\SeancezoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeancezoneRepository::class)
 */
class Seancezone extends SeanceEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="seancezones")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Officiant::class, inversedBy="seancezones")
     */
    private $officiant;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="seancezones")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="seancezones")
     */
    private $zone;

    /**
     * @ORM\OneToMany(targetEntity=Presencezone::class, mappedBy="seancezone")
     */
    private $presencezones;

    /**
     * @ORM\OneToMany(targetEntity=Invitezone::class, mappedBy="seancezone")
     */
    private $invitezones;

    public function __construct()
    {
        $this->presencezones = new ArrayCollection();
        $this->invitezones = new ArrayCollection();
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

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }
    public function __toString() {
        return $this->getDatesuper()->format('d-m-Y');
    }

    /**
     * @return Collection<int, Presencezone>
     */
    public function getPresencezones(): Collection
    {
        return $this->presencezones;
    }

    public function addPresencezone(Presencezone $presencezone): self
    {
        if (!$this->presencezones->contains($presencezone)) {
            $this->presencezones[] = $presencezone;
            $presencezone->setSeancezone($this);
        }

        return $this;
    }

    public function removePresencezone(Presencezone $presencezone): self
    {
        if ($this->presencezones->removeElement($presencezone)) {
            // set the owning side to null (unless already changed)
            if ($presencezone->getSeancezone() === $this) {
                $presencezone->setSeancezone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitezone>
     */
    public function getInvitezones(): Collection
    {
        return $this->invitezones;
    }

    public function addInvitezone(Invitezone $invitezone): self
    {
        if (!$this->invitezones->contains($invitezone)) {
            $this->invitezones[] = $invitezone;
            $invitezone->setSeancezone($this);
        }

        return $this;
    }

    public function removeInvitezone(Invitezone $invitezone): self
    {
        if ($this->invitezones->removeElement($invitezone)) {
            // set the owning side to null (unless already changed)
            if ($invitezone->getSeancezone() === $this) {
                $invitezone->setSeancezone(null);
            }
        }

        return $this;
    }
}
