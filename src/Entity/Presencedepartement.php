<?php

namespace App\Entity;

use App\Repository\PresencedepartementRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PresencedepartementRepository::class)
 */
class Presencedepartement extends AbstractEntity 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="presencedepartements")
     */
    private $departement;

  /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="presencedepartements")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Seancedepartement::class, inversedBy="presencedepartements")
     */
    private $seancedepartement;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="presencedepartements")
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

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

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

    public function getSeancedepartement(): ?Seancedepartement
    {
        return $this->seancedepartement;
    }

    public function setSeancedepartement(?Seancedepartement $seancedepartement): self
    {
        $this->seancedepartement = $seancedepartement;

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
