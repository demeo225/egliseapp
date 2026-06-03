<?php

namespace App\Entity;

use App\Repository\DepartementfideleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepartementfideleRepository::class)
 */
class Departementfidele
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datedep;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatdepfidele;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $roledepfidele;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="departementfideles")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="departementfideles")
     */
    private $departement;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="departementfidele")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedep(): ?\DateTimeInterface
    {
        return $this->datedep;
    }

    public function setDatedep(?\DateTimeInterface $datedep): self
    {
        $this->datedep = $datedep;

        return $this;
    }

    public function getEtatdepfidele(): ?bool
    {
        return $this->etatdepfidele;
    }

    public function setEtatdepfidele(?bool $etatdepfidele): self
    {
        $this->etatdepfidele = $etatdepfidele;

        return $this;
    }

    public function getRoledepfidele(): ?string
    {
        return $this->roledepfidele;
    }

    public function setRoledepfidele(?string $roledepfidele): self
    {
        $this->roledepfidele = $roledepfidele;

        return $this;
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

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

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
