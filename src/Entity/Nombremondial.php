<?php

namespace App\Entity;

use App\Repository\NombremondialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NombremondialRepository::class)
 */
class Nombremondial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hommem;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $femmem;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $garconm;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $enfantm;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $adultem;

    /**
     * @ORM\ManyToOne(targetEntity=Communaute::class, inversedBy="nombremondials")
     */
    private $communaute;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fillem;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHommem(): ?int
    {
        return $this->hommem;
    }

    public function setHommem(?int $hommem): self
    {
        $this->hommem = $hommem;

        return $this;
    }

    public function getFemmem(): ?int
    {
        return $this->femmem;
    }

    public function setFemmem(?int $femmem): self
    {
        $this->femmem = $femmem;

        return $this;
    }

    public function getGarconm(): ?int
    {
        return $this->garconm;
    }

    public function setGarconm(?int $garconm): self
    {
        $this->garconm = $garconm;

        return $this;
    }

    public function getEnfantm(): ?int
    {
        return $this->enfantm;
    }

    public function setEnfantm(?int $enfantm): self
    {
        $this->enfantm = $enfantm;

        return $this;
    }

    public function getAdultem(): ?int
    {
        return $this->adultem;
    }

    public function setAdultem(?int $adultem): self
    {
        $this->adultem = $adultem;

        return $this;
    }

    public function getCommunaute(): ?Communaute
    {
        return $this->communaute;
    }

    public function setCommunaute(?Communaute $communaute): self
    {
        $this->communaute = $communaute;

        return $this;
    }

    public function getFillem(): ?int
    {
        return $this->fillem;
    }

    public function setFillem(?int $fillem): self
    {
        $this->fillem = $fillem;

        return $this;
    }
}
