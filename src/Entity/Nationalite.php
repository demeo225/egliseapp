<?php

namespace App\Entity;

use App\Repository\NationaliteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=NationaliteRepository::class)
 */
class Nationalite extends AbstractEntity
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
    private $libelle;

    /**
     *  @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     *  @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;
    
    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="nationalite")
     */
    private $enfants;

    /**
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="nationalite")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
        $this->fidele = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
    
    public function __toString() {
      return  $this->getLibelle();
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

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setNationalite($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getNationalite() === $this) {
                $enfant->setNationalite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fidele[]
     */
    public function getFidele(): Collection
    {
        return $this->fidele;
    }

    public function addFidele(Fidele $fidele): self
    {
        if (!$this->fidele->contains($fidele)) {
            $this->fidele[] = $fidele;
            $fidele->setNationalite($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fidele->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getNationalite() === $this) {
                $fidele->setNationalite(null);
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
    
}
