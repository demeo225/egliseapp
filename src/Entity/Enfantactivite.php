<?php

namespace App\Entity;

use App\Repository\EnfantactiviteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EnfantactiviteRepository::class)
 */
class Enfantactivite extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Enfant::class)
     */
    private $enfant;



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
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $montantpayer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reste;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="enfantactivites")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Ecodimactivite::class, inversedBy="enfantactivites")
     */
    private $ecodimactivite;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecotiser;

 
    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

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



    public function getMontantpayer(): ?int
    {
        return $this->montantpayer;
    }

    public function setMontantpayer(?int $montantpayer): self
    {
        $this->montantpayer = $montantpayer;

        return $this;
    }

    public function getReste(): ?int
    {
        return $this->reste;
    }

    public function setReste(?int $reste): self
    {
        $this->reste = $reste;

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

    public function getEcodimactivite(): ?Ecodimactivite
    {
        return $this->ecodimactivite;
    }

    public function setEcodimactivite(?Ecodimactivite $ecodimactivite): self
    {
        $this->ecodimactivite = $ecodimactivite;

        return $this;
    }

    public function getDatecotiser(): ?\DateTimeInterface
    {
        return $this->datecotiser;
    }

    public function setDatecotiser(?\DateTimeInterface $datecotiser): self
    {
        $this->datecotiser = $datecotiser;

        return $this;
    }

   
}
