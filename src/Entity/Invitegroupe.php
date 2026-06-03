<?php

namespace App\Entity;

use App\Repository\InvitegroupeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitegroupeRepository::class)
 */
class Invitegroupe extends InviteEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Seancegroupe::class, inversedBy="invitegroupes")
     */
    private $seancegroupe;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="invitegroupes")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="invitegroupes")
     */
    private $groupe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeancegroupe(): ?Seancegroupe
    {
        return $this->seancegroupe;
    }

    public function setSeancegroupe(?Seancegroupe $seancegroupe): self
    {
        $this->seancegroupe = $seancegroupe;

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
}
