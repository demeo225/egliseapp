<?php

namespace App\Entity;

use App\Repository\InvitefamilleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitefamilleRepository::class)
 */
class Invitefamille extends InviteEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Seancefamille::class, inversedBy="invitefamilles")
     */
    private $seancefamille;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="invitefamilles")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeancefamille(): ?Seancefamille
    {
        return $this->seancefamille;
    }

    public function setSeancefamille(?Seancefamille $seancefamille): self
    {
        $this->seancefamille = $seancefamille;

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
