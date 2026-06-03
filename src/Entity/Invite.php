<?php

namespace App\Entity;

use App\Repository\InviteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=InviteRepository::class)
 */
class Invite extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups("public")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     * @Groups("public")
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $profession;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("public")
     */
    private $createAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Culte::class, inversedBy="invites")
     * @Groups("public")
     */
    private $culte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $habitation;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="invites")
     * @Groups("public")
     */
    private $fidele;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $invitant;

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

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

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

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;

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
        return $this->getNom();;
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

    public function getCulte(): ?Culte
    {
        return $this->culte;
    }

    public function setCulte(?Culte $culte): self
    {
        $this->culte = $culte;

        return $this;
    }

    public function getHabitation(): ?string
    {
        return $this->habitation;
    }

    public function setHabitation(?string $habitation): self
    {
        $this->habitation = $habitation;

        return $this;
    }

    public function getFidele(): ?Fidele
    {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self
    {
        $this->fidele = $fidele;

        return $this;
    }

    public function getInvitant(): ?string
    {
        return $this->invitant;
    }

    public function setInvitant(?string $invitant): self
    {
        $this->invitant = $invitant;

        return $this;
    }
}
