<?php

namespace App\Entity;

use App\Repository\AmeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AmeRepository::class)
 */
class Ame extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     * [Assert\Regex(
        pattern: '/^[a-z]+$/i',
        htmlPattern: '^[a-zA-Z]+$'
    )
     */
    private $nom;
    
    
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
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $habitation;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $rdv;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $converti;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $invitation;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="ames")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Evangelisation::class, inversedBy="ames")
     * @Groups("public")
     */
    private $evangelisation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
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

    public function getHabitation(): ?string
    {
        return $this->habitation;
    }

    public function setHabitation(?string $habitation): self
    {
        $this->habitation = $habitation;

        return $this;
    }

    public function getRdv(): ?string
    {
        return $this->rdv;
    }

    public function setRdv(?string $rdv): self
    {
        $this->rdv = $rdv;

        return $this;
    }

    public function getConverti(): ?string
    {
        return $this->converti;
    }

    public function setConverti(?string $converti): self
    {
        $this->converti = $converti;

        return $this;
    }

    public function getInvitation(): ?string
    {
        return $this->invitation;
    }

    public function setInvitation(?string $invitation): self
    {
        $this->invitation = $invitation;

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

    public function getEvangelisation(): ?Evangelisation
    {
        return $this->evangelisation;
    }

    public function setEvangelisation(?Evangelisation $evangelisation): self
    {
        $this->evangelisation = $evangelisation;

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
}
