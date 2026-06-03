<?php

namespace App\Entity;

use App\Repository\PresencecelluleRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PresencecelluleRepository::class)
 */
class Presencecellule extends AbstractEntity 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="presencecellules")
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="presencecellules")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Seancecellule::class, inversedBy="presencecellules")
     */
    private $seancecellule;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="presencecellules")
     */
    private $fidele;

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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
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

    public function getFidele(): ?Fidele
    {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self
    {
        $this->fidele = $fidele;

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
}
