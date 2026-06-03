<?php

namespace App\Entity;

use App\Repository\FideleenfantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FideleenfantRepository::class)
 */
class Fideleenfant extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="fideleenfants")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Enfant::class)
     */
    private $enfant;

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
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }
}
