<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DonRepository::class)
 */
class Don extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $donateur;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $nature;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datedon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valeurdon;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatdon;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $ajout;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $typeoff;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDonateur(): ?string
    {
        return $this->donateur;
    }

    public function setDonateur(string $donateur): self
    {
        $this->donateur = $donateur;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(?string $nature): self
    {
        $this->nature = $nature;

        return $this;
    }

    public function getDatedon(): ?\DateTimeInterface
    {
        return $this->datedon;
    }

    public function setDatedon(?\DateTimeInterface $datedon): self
    {
        $this->datedon = $datedon;

        return $this;
    }

    public function getValeurdon(): ?int
    {
        return $this->valeurdon;
    }

    public function setValeurdon(?int $valeurdon): self
    {
        $this->valeurdon = $valeurdon;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEtatdon(): ?bool
    {
        return $this->etatdon;
    }

    public function setEtatdon(?bool $etatdon): self
    {
        $this->etatdon = $etatdon;

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

    public function getAjout(): ?string
    {
        return $this->ajout;
    }

    public function setAjout(?string $ajout): self
    {
        $this->ajout = $ajout;

        return $this;
    }

    public function getTypeoff(): ?string
    {
        return $this->typeoff;
    }

    public function setTypeoff(?string $typeoff): self
    {
        $this->typeoff = $typeoff;

        return $this;
    }
}
