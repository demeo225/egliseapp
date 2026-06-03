<?php

namespace App\Entity;

use App\Repository\CotisergroupeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CotisergroupeRepository::class)
 */
class Cotisergroupe extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $montantpayer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reste;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class)
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

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
     * @ORM\ManyToOne(targetEntity=Groupe::class)
     */
    private $groupe;

    

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecotiser;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisationgroupe::class, inversedBy="cotisergroupes")
     */
    private $cotisationgroupe;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFidele(): ?Fidele
    {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self
    {
        $this->fidele = $fidele;

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

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

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

    public function getCotisationgroupe(): ?Cotisationgroupe
    {
        return $this->cotisationgroupe;
    }

    public function setCotisationgroupe(?Cotisationgroupe $cotisationgroupe): self
    {
        $this->cotisationgroupe = $cotisationgroupe;

        return $this;
    }
}
