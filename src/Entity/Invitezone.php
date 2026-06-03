<?php

namespace App\Entity;

use App\Repository\InvitezoneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitezoneRepository::class)
 */
class Invitezone extends InviteEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Seancezone::class, inversedBy="invitezones")
     */
    private $seancezone;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="invitezones")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeancezone(): ?Seancezone
    {
        return $this->seancezone;
    }

    public function setSeancezone(?Seancezone $seancezone): self
    {
        $this->seancezone = $seancezone;

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
