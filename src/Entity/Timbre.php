<?php

namespace App\Entity;

use App\Repository\TimbreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimbreRepository::class)
 */
class Timbre  extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $annee;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateachat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ideglise;

    /**
     * @ORM\ManyToOne(targetEntity=Cartemembre::class, inversedBy="timbres")
     */
    private $cartemembre;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="timbres")
     */
    private $eglise;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getDateachat(): ?\DateTimeInterface
    {
        return $this->dateachat;
    }

    public function setDateachat(?\DateTimeInterface $dateachat): self
    {
        $this->dateachat = $dateachat;

        return $this;
    }

    public function getIdeglise(): ?int
    {
        return $this->ideglise;
    }

    public function setIdeglise(?int $ideglise): self
    {
        $this->ideglise = $ideglise;

        return $this;
    }

    public function getCartemembre(): ?Cartemembre
    {
        return $this->cartemembre;
    }

    public function setCartemembre(?Cartemembre $cartemembre): self
    {
        $this->cartemembre = $cartemembre;

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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
