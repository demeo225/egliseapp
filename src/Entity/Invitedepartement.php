<?php

namespace App\Entity;

use App\Repository\InvitedepartementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitedepartementRepository::class)
 */
class Invitedepartement extends InviteEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Seancedepartement::class, inversedBy="invitedepartements")
     */
    private $seancedepartement;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="invitedepartements")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeancedepartement(): ?Seancedepartement
    {
        return $this->seancedepartement;
    }

    public function setSeancedepartement(?Seancedepartement $seancedepartement): self
    {
        $this->seancedepartement = $seancedepartement;

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
