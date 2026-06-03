<?php

namespace App\Entity;

use App\Repository\InvitecelluleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitecelluleRepository::class)
 */
class Invitecellule extends InviteEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Seancecellule::class, inversedBy="invitecellules")
     */
    private $seancecellule;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="invitecellules")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeancecellule(): ?Seancecellule
    {
        return $this->seancecellule;
    }

    public function setSeancecellule(?Seancecellule $seancecellule): self
    {
        $this->seancecellule = $seancecellule;

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
}
