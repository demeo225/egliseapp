<?php

namespace App\Entity;

use App\Repository\CommunauteRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=CommunauteRepository::class)
 */
class Communaute extends AbstractEntity
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $agrement;

    /**
     * @ORM\OneToMany(targetEntity=Eglise::class, mappedBy="communaute")
     */
    private $eglises;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $verset;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $texte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siege;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    public function __construct()
    {
        $this->eglises = new ArrayCollection();
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

    public function getAgrement(): ?string
    {
        return $this->agrement;
    }

    public function setAgrement(?string $agrement): self
    {
        $this->agrement = $agrement;

        return $this;
    }

    /**
     * @return Collection|Eglise[]
     */
    public function getEglises(): Collection
    {
        return $this->eglises;
    }

    public function addEglise(Eglise $eglise): self
    {
        if (!$this->eglises->contains($eglise)) {
            $this->eglises[] = $eglise;
            $eglise->setCommunaute($this);
        }

        return $this;
    }

    public function removeEglise(Eglise $eglise): self
    {
        if ($this->eglises->removeElement($eglise)) {
            // set the owning side to null (unless already changed)
            if ($eglise->getCommunaute() === $this) {
                $eglise->setCommunaute(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->libelle;
    }

    public function getCreateAt(): ?DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(?DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getVerset(): ?string
    {
        return $this->verset;
    }

    public function setVerset(?string $verset): self
    {
        $this->verset = $verset;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSiege(): ?string
    {
        return $this->siege;
    }

    public function setSiege(?string $siege): self
    {
        $this->siege = $siege;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
}
