<?php

namespace App\Entity;

use App\Repository\CoupleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CoupleRepository::class)
 */
class Couple extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="couples")
     */
    private $eglise;

    /**
     * @ORM\OneToOne(targetEntity=Fidele::class, cascade={"persist", "remove"})
     */
    private $epoux;

    /**
     * @ORM\OneToOne(targetEntity=Fidele::class, cascade={"persist", "remove"})
     */
    private $epouse;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEpoux(): ?Fidele
    {
        return $this->epoux;
    }

    public function setEpoux(?Fidele $epoux): self
    {
        $this->epoux = $epoux;

        return $this;
    }

    public function getEpouse(): ?Fidele
    {
        return $this->epouse;
    }

    public function setEpouse(?Fidele $epouse): self
    {
        $this->epouse = $epouse;

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
