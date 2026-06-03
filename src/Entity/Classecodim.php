<?php

namespace App\Entity;

use App\Repository\ClassecodimRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ClassecodimRepository::class)
 */
class Classecodim extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $description;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

   

    /**
     * @ORM\OneToMany(targetEntity=Inscrire::class, mappedBy="classecodim")
     */
    private $inscrires;

    /**
     * @ORM\OneToMany(targetEntity=Cultecodim::class, mappedBy="classecodim")
     */
    private $cultecodims;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="classecodim")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Presenceculteecodim::class, mappedBy="classecodim")
     */
    private $presenceculteecodims;

    public function __construct()
    {
        $this->inscrires = new ArrayCollection();
        $this->cultecodims = new ArrayCollection();
        $this->presenceculteecodims = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }


    public function __toString() {
       return $this->getNom();
    }

    /**
     * @return Collection|Inscrire[]
     */
    public function getInscrires(): Collection
    {
        return $this->inscrires;
    }

    public function addInscrire(Inscrire $inscrire): self
    {
        if (!$this->inscrires->contains($inscrire)) {
            $this->inscrires[] = $inscrire;
            $inscrire->setClassecodim($this);
        }

        return $this;
    }

    public function removeInscrire(Inscrire $inscrire): self
    {
        if ($this->inscrires->removeElement($inscrire)) {
            // set the owning side to null (unless already changed)
            if ($inscrire->getClassecodim() === $this) {
                $inscrire->setClassecodim(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cultecodim[]
     */
    public function getCultecodims(): Collection
    {
        return $this->cultecodims;
    }

    public function addCultecodim(Cultecodim $cultecodim): self
    {
        if (!$this->cultecodims->contains($cultecodim)) {
            $this->cultecodims[] = $cultecodim;
            $cultecodim->setClassecodim($this);
        }

        return $this;
    }

    public function removeCultecodim(Cultecodim $cultecodim): self
    {
        if ($this->cultecodims->removeElement($cultecodim)) {
            // set the owning side to null (unless already changed)
            if ($cultecodim->getClassecodim() === $this) {
                $cultecodim->setClassecodim(null);
            }
        }

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

    /**
     * @return Collection<int, Presenceculteecodim>
     */
    public function getPresenceculteecodims(): Collection
    {
        return $this->presenceculteecodims;
    }

    public function addPresenceculteecodim(Presenceculteecodim $presenceculteecodim): self
    {
        if (!$this->presenceculteecodims->contains($presenceculteecodim)) {
            $this->presenceculteecodims[] = $presenceculteecodim;
            $presenceculteecodim->setClassecodim($this);
        }

        return $this;
    }

    public function removePresenceculteecodim(Presenceculteecodim $presenceculteecodim): self
    {
        if ($this->presenceculteecodims->removeElement($presenceculteecodim)) {
            // set the owning side to null (unless already changed)
            if ($presenceculteecodim->getClassecodim() === $this) {
                $presenceculteecodim->setClassecodim(null);
            }
        }

        return $this;
    }
    
    
}

