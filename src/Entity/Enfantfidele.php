<?php

namespace App\Entity;

use App\Repository\EnfantfideleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnfantfideleRepository::class)
 */
class Enfantfidele extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="enfantfideles")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Enfant::class, inversedBy="enfantfideles")
     */
    private $Enfant;

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

    public function getEnfant(): ?Enfant
    {
        return $this->Enfant;
    }

    public function setEnfant(?Enfant $Enfant): self
    {
        $this->Enfant = $Enfant;

        return $this;
    }
}
