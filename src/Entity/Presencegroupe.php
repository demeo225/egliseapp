<?php

namespace App\Entity;

use App\Repository\PresencegroupeRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PresencegroupeRepository::class)
 */
class Presencegroupe extends AbstractEntity 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="presencegroupes")
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="presencegroupes")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Seancegroupe::class, inversedBy="presencegroupes")
     */
    private $seancegroupe;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="presencegroupes")
     */
    private $fidele;

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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }

    public function getSeancegroupe(): ?Seancegroupe
    {
        return $this->seancegroupe;
    }

    public function setSeancegroupe(?Seancegroupe $seancegroupe): self
    {
        $this->seancegroupe = $seancegroupe;

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
