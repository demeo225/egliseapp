<?php

namespace App\Entity;

use App\Repository\FiancailleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FiancailleRepository::class)
 */
class Fiancaille extends AbstractEntity 
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
    private $datefiancaille;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $fiancee;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $fiance;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $pasteurfiancaille;

   

  

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
    private $etatfiancaille;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="fiancaille")
     */
    private $eglise;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typefiancaille;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typefiancee;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datemariage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mariage;
  /** 
     * @ORM\OneToOne(targetEntity=Fidele::class, inversedBy="fiancailleFiance")
     * @ORM\JoinColumn(name="fiance_membre_id", referencedColumnName="id", nullable=true)
     */
    private $fiancemembre;

    /**
     * @ORM\OneToOne(targetEntity=Fidele::class, inversedBy="fiancailleFiancee")
     * @ORM\JoinColumn(name="fiancee_membre_id", referencedColumnName="id", nullable=true)
     */
    private $fianceemembre;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatefiancaille(): ?\DateTimeInterface
    {
        return $this->datefiancaille;
    }

    public function setDatefiancaille(?\DateTimeInterface $datefiancaille): self
    {
        $this->datefiancaille = $datefiancaille;
        return $this;
    }

    public function getFiancee(): ?string
    {
        return $this->fiancee;
    }

    public function setFiancee(?string $fiancee): self
    {
        $this->fiancee = $fiancee;
        return $this;
    }

    public function getFiance(): ?string
    {
        return $this->fiance;
    }

    public function setFiance(?string $fiance): self
    {
        $this->fiance = $fiance;
        return $this;
    }

    public function getPasteurfiancaille(): ?string
    {
        return $this->pasteurfiancaille;
    }

    public function setPasteurfiancaille(?string $pasteurfiancaille): self
    {
        $this->pasteurfiancaille = $pasteurfiancaille;
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

    public function getEtatfiancaille(): ?bool
    {
        return $this->etatfiancaille;
    }

    public function setEtatfiancaille(?bool $etatfiancaille): self
    {
        $this->etatfiancaille = $etatfiancaille;
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

    public function getTypefiancaille(): ?bool
    {
        return $this->typefiancaille;
    }

    public function setTypefiancaille(?bool $typefiancaille): self
    {
        $this->typefiancaille = $typefiancaille;
        return $this;
    }

    public function getTypefiancee(): ?bool
    {
        return $this->typefiancee;
    }

    public function setTypefiancee(?bool $typefiancee): self
    {
        $this->typefiancee = $typefiancee;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getDatemariage(): ?\DateTimeInterface
    {
        return $this->datemariage;
    }

    public function setDatemariage(?\DateTimeInterface $datemariage): self
    {
        $this->datemariage = $datemariage;
        return $this;
    }

    public function getMariage(): ?bool
    {
        return $this->mariage;
    }

    public function setMariage(?bool $mariage): self
    {
        $this->mariage = $mariage;
        return $this;
    }

    public function getFiancemembre(): ?Fidele
    {
        return $this->fiancemembre;
    }

    public function setFiancemembre(?Fidele $fiancemembre): self
    {
        $this->fiancemembre = $fiancemembre;
        return $this;
    }

    public function getFianceemembre(): ?Fidele
    {
        return $this->fianceemembre;
    }

    public function setFianceemembre(?Fidele $fianceemembre): self
    {
        $this->fianceemembre = $fianceemembre;
        return $this;
    }
}