<?php

namespace App\Entity;

use App\Repository\DecesRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=DecesRepository::class)
 */
class Deces extends AbstractEntity
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
    private $datedeces;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieudeces;



    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\OneToOne(targetEntity=Fidele::class, cascade={"persist", "remove"})
     */
    private $fidele;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $raisondeces;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="deces")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedeces(): ?\DateTimeInterface
    {
        return $this->datedeces;
    }

    public function setDatedeces(?\DateTimeInterface $datedeces): self
    {
        $this->datedeces = $datedeces;

        return $this;
    }

    public function getLieudeces(): ?string
    {
        return $this->lieudeces;
    }

    public function setLieudeces(?string $lieudeces): self
    {
        $this->lieudeces = $lieudeces;

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

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

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

    public function getRaisondeces(): ?string
    {
        return $this->raisondeces;
    }

    public function setRaisondeces(?string $raisondeces): self
    {
        $this->raisondeces = $raisondeces;

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
