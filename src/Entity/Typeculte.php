<?php

namespace App\Entity;

use App\Repository\TypeculteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeculteRepository::class)
 */
class Typeculte extends AbstractEntity
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
    private $libelle;

  

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heuredebut;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heurefin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ideglise;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="typecultes")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Culte::class, mappedBy="typeculte")
     */
    private $cultes;


    public function __construct()
    {
        $this->cultes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

       public function getHeuredebut(): ?\DateTimeInterface
    {
        return $this->heuredebut;
    }

    public function setHeuredebut(?\DateTimeInterface $heuredebut): self
    {
        $this->heuredebut = $heuredebut;

        return $this;
    }

    public function getHeurefin(): ?\DateTimeInterface
    {
        return $this->heurefin;
    }

    public function setHeurefin(?\DateTimeInterface $heurefin): self
    {
        $this->heurefin = $heurefin;

        return $this;
    }

    public function getIdeglise(): ?int
    {
        return $this->ideglise;
    }

    public function setIdeglise(?int $ideglise): self
    {
        $this->ideglise = $ideglise;

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
     * @return Collection<int, Culte>
     */
    public function getCultes(): Collection
    {
        return $this->cultes;
    }

    public function addCulte(Culte $culte): self
    {
        if (!$this->cultes->contains($culte)) {
            $this->cultes[] = $culte;
            $culte->setTypeculte($this);
        }

        return $this;
    }

    public function removeCulte(Culte $culte): self
    {
        if ($this->cultes->removeElement($culte)) {
            // set the owning side to null (unless already changed)
            if ($culte->getTypeculte() === $this) {
                $culte->setTypeculte(null);
            }
        }

        return $this;
    }

}
