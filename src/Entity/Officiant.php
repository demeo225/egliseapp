<?php

namespace App\Entity;

use App\Repository\OfficiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=OfficiantRepository::class)
 */
class Officiant extends AbstractEntity
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
    private $nomofficiant;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $titre;

    /**
     * @Gedmo\Slug(fields={"nomofficiant"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Seancezone::class, mappedBy="officiant")
     */
    private $seancezones;

    public function __construct()
    {
        $this->seancezones = new ArrayCollection();
    }

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomofficiant(): ?string
    {
        return $this->nomofficiant;
    }

    public function setNomofficiant(?string $nomofficiant): self
    {
        $this->nomofficiant = $nomofficiant;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Seancezone>
     */
    public function getSeancezones(): Collection
    {
        return $this->seancezones;
    }

    public function addSeancezone(Seancezone $seancezone): self
    {
        if (!$this->seancezones->contains($seancezone)) {
            $this->seancezones[] = $seancezone;
            $seancezone->setOfficiant($this);
        }

        return $this;
    }

    public function removeSeancezone(Seancezone $seancezone): self
    {
        if ($this->seancezones->removeElement($seancezone)) {
            // set the owning side to null (unless already changed)
            if ($seancezone->getOfficiant() === $this) {
                $seancezone->setOfficiant(null);
            }
        }

        return $this;
    }

    
}
