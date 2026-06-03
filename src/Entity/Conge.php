<?php

namespace App\Entity;

use App\Repository\CongeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CongeRepository::class)
 */
class Conge  extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateconge;

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
     * @ORM\Column(type="date", nullable=true)
     */
    private $datefin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cause;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detail;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="conges")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="conges")
     */
    private $eglise;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    public function getId(): ?int {
        return $this->id;
    }

    public function getDateconge(): ?\DateTimeInterface {
        return $this->dateconge;
    }

    public function setDateconge(?\DateTimeInterface $dateconge): self {
        $this->dateconge = $dateconge;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): self {
        $this->datefin = $datefin;

        return $this;
    }

    public function getCause(): ?string {
        return $this->cause;
    }

    public function setCause(?string $cause): self {
        $this->cause = $cause;

        return $this;
    }

    public function getDetail(): ?string {
        return $this->detail;
    }

    public function setDetail(?string $detail): self {
        $this->detail = $detail;

        return $this;
    }

    public function getFidele(): ?Fidele {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self {
        $this->fidele = $fidele;

        return $this;
    }

    public function getEglise(): ?Eglise {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self {
        $this->eglise = $eglise;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): self {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self {
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

}
