<?php

namespace App\Entity;

use App\Repository\EcodimactiviteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=EcodimactiviteRepository::class)
 */
class Ecodimactivite extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateactivite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable2;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomactivite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $participation;

    /**
     * @ORM\OneToMany(targetEntity=Enfantactivite::class, mappedBy="ecodimactivite")
     */
    private $enfantactivites;

    /**
     * @ORM\OneToMany(targetEntity=Detailenfantactivite::class, mappedBy="ecodimactivite")
     */
    private $detailenfantactivites;

    public function __construct()
    {
        $this->enfantactivites = new ArrayCollection();
        $this->detailenfantactivites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDateactivite(): ?\DateTimeInterface
    {
        return $this->dateactivite;
    }

    public function setDateactivite(?\DateTimeInterface $dateactivite): self
    {
        $this->dateactivite = $dateactivite;

        return $this;
    }

    public function getResponsable1(): ?string
    {
        return $this->responsable1;
    }

    public function setResponsable1(?string $responsable1): self
    {
        $this->responsable1 = $responsable1;

        return $this;
    }

    public function getResponsable2(): ?string
    {
        return $this->responsable2;
    }

    public function setResponsable2(?string $responsable2): self
    {
        $this->responsable2 = $responsable2;

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

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): self
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNomactivite(): ?string
    {
        return $this->nomactivite;
    }

    public function setNomactivite(?string $nomactivite): self
    {
        $this->nomactivite = $nomactivite;

        return $this;
    }

    public function getParticipation(): ?int
    {
        return $this->participation;
    }

    public function setParticipation(?int $participation): self
    {
        $this->participation = $participation;

        return $this;
    }
    
    public function __toString() {
        return $this->nomactivite;
    }

    /**
     * @return Collection<int, Enfantactivite>
     */
    public function getEnfantactivites(): Collection
    {
        return $this->enfantactivites;
    }

    public function addEnfantactivite(Enfantactivite $enfantactivite): self
    {
        if (!$this->enfantactivites->contains($enfantactivite)) {
            $this->enfantactivites[] = $enfantactivite;
            $enfantactivite->setEcodimactivite($this);
        }

        return $this;
    }

    public function removeEnfantactivite(Enfantactivite $enfantactivite): self
    {
        if ($this->enfantactivites->removeElement($enfantactivite)) {
            // set the owning side to null (unless already changed)
            if ($enfantactivite->getEcodimactivite() === $this) {
                $enfantactivite->setEcodimactivite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailenfantactivite>
     */
    public function getDetailenfantactivites(): Collection
    {
        return $this->detailenfantactivites;
    }

    public function addDetailenfantactivite(Detailenfantactivite $detailenfantactivite): self
    {
        if (!$this->detailenfantactivites->contains($detailenfantactivite)) {
            $this->detailenfantactivites[] = $detailenfantactivite;
            $detailenfantactivite->setEcodimactivite($this);
        }

        return $this;
    }

    public function removeDetailenfantactivite(Detailenfantactivite $detailenfantactivite): self
    {
        if ($this->detailenfantactivites->removeElement($detailenfantactivite)) {
            // set the owning side to null (unless already changed)
            if ($detailenfantactivite->getEcodimactivite() === $this) {
                $detailenfantactivite->setEcodimactivite(null);
            }
        }

        return $this;
    }
}
