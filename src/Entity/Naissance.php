<?php

namespace App\Entity;

use App\Repository\NaissanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NaissanceRepository::class)
 */
class Naissance extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datenaissance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieunaissance;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $poidsnaiss;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sexenaiss;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $heurenaiss;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $perenaiss;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $merenaiss;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $naturedon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $especedon;

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
     * @ORM\Column(type="string", length=255)
     */
    private $nomnaiss;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoFile;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="naissance")
     */
    private $eglise;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typepere;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typemere;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="naissance")
     */
    private $perenaisse;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="naissance")
     */
    private $merenaisse;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datepresentation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatenaissance(): ?\DateTimeInterface
    {
        return $this->datenaissance;
    }

    public function setDatenaissance(?\DateTimeInterface $datenaissance): self
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    public function getLieunaissance(): ?string
    {
        return $this->lieunaissance;
    }

    public function setLieunaissance(?string $lieunaissance): self
    {
        $this->lieunaissance = $lieunaissance;

        return $this;
    }

    public function getPoidsnaiss(): ?string
    {
        return $this->poidsnaiss;
    }

    public function setPoidsnaiss(?string $poidsnaiss): self
    {
        $this->poidsnaiss = $poidsnaiss;

        return $this;
    }

    public function getSexenaiss(): ?bool
    {
        return $this->sexenaiss;
    }

    public function setSexenaiss(?bool $sexenaiss): self
    {
        $this->sexenaiss = $sexenaiss;

        return $this;
    }

    public function getHeurenaiss(): ?string
    {
        return $this->heurenaiss;
    }

    public function setHeurenaiss(?string $heurenaiss): self
    {
        $this->heurenaiss = $heurenaiss;

        return $this;
    }

    public function getPerenaiss(): ?string
    {
        return $this->perenaiss;
    }

    public function setPerenaiss(?string $perenaiss): self
    {
        $this->perenaiss = $perenaiss;

        return $this;
    }

    public function getMerenaiss(): ?string
    {
        return $this->merenaiss;
    }

    public function setMerenaiss(?string $merenaiss): self
    {
        $this->merenaiss = $merenaiss;

        return $this;
    }

    public function getNaturedon(): ?string
    {
        return $this->naturedon;
    }

    public function setNaturedon(?string $naturedon): self
    {
        $this->naturedon = $naturedon;

        return $this;
    }

    public function getEspecedon(): ?int
    {
        return $this->especedon;
    }

    public function setEspecedon(?int $especedon): self
    {
        $this->especedon = $especedon;

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

    public function getNomnaiss(): ?string
    {
        return $this->nomnaiss;
    }

    public function setNomnaiss(string $nomnaiss): self
    {
        $this->nomnaiss = $nomnaiss;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhotoFile(): ?string
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?string $photoFile): self
    {
        $this->photoFile = $photoFile;

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

    public function getTypepere(): ?bool
    {
        return $this->typepere;
    }

    public function setTypepere(?bool $typepere): self
    {
        $this->typepere = $typepere;

        return $this;
    }

    public function getTypemere(): ?bool
    {
        return $this->typemere;
    }

    public function setTypemere(?bool $typemere): self
    {
        $this->typemere = $typemere;

        return $this;
    }

    public function getPerenaisse(): ?Fidele
    {
        return $this->perenaisse;
    }

    public function setPerenaisse(?Fidele $perenaisse): self
    {
        $this->perenaisse = $perenaisse;

        return $this;
    }

    public function getMerenaisse(): ?Fidele
    {
        return $this->merenaisse;
    }

    public function setMerenaisse(?Fidele $merenaisse): self
    {
        $this->merenaisse = $merenaisse;

        return $this;
    }

    public function getDatepresentation(): ?\DateTimeInterface
    {
        return $this->datepresentation;
    }

    public function setDatepresentation(?\DateTimeInterface $datepresentation): self
    {
        $this->datepresentation = $datepresentation;

        return $this;
    }
}
