<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\MappedSuperclass
 *
 * @author Lynova Tech
 */
class InviteEntity extends AbstractEntity {

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
     * @Groups("public")
     */
    private $nom;

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
    private $sexe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $fonction;

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getHabitation(): ?string {
        return $this->habitation;
    }

    public function setHabitation(?string $habitation): self {
        $this->habitation = $habitation;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): self {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getSexe(): ?string {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self {
        $this->sexe = $sexe;

        return $this;
    }

    public function getFonction(): ?string {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): self {
        $this->fonction = $fonction;

        return $this;
    }

    public function getContact(): ?string {
        return $this->contact;
    }

    public function setContact(string $contact): self {
        $this->contact = $contact;

        return $this;
    }

}
