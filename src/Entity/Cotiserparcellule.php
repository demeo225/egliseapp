<?php

namespace App\Entity;

use App\Repository\CotiserparcelluleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=CotiserparcelluleRepository::class)
 */
class Cotiserparcellule extends AbstractEntity
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
    private $montantpayer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reste;


    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecotiser;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cotiserparcellules")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="cotiserparcellules")
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisationparcellule::class, inversedBy="cotiserparcellules")
     */
    private $cotisationparcellule;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $restecotiser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantpayer(): ?int
    {
        return $this->montantpayer;
    }

    public function setMontantpayer(?int $montantpayer): self
    {
        $this->montantpayer = $montantpayer;

        return $this;
    }

    public function getReste(): ?int
    {
        return $this->reste;
    }

    public function setReste(?int $reste): self
    {
        $this->reste = $reste;

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

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDatecotiser(): ?\DateTimeInterface
    {
        return $this->datecotiser;
    }

    public function setDatecotiser(?\DateTimeInterface $datecotiser): self
    {
        $this->datecotiser = $datecotiser;

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

    public function getCellule(): ?Cellule
    {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self
    {
        $this->cellule = $cellule;

        return $this;
    }

    public function getCotisationparcellule(): ?Cotisationparcellule
    {
        return $this->cotisationparcellule;
    }

    public function setCotisationparcellule(?Cotisationparcellule $cotisationparcellule): self
    {
        $this->cotisationparcellule = $cotisationparcellule;

        return $this;
    }

    public function getRestecotiser(): ?int
    {
        return $this->restecotiser;
    }

    public function setRestecotiser(?int $restecotiser): self
    {
        $this->restecotiser = $restecotiser;

        return $this;
    }
}
