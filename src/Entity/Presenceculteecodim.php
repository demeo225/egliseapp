<?php

namespace App\Entity;

use App\Repository\PresenceculteecodimRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PresenceculteecodimRepository::class)
 */
class Presenceculteecodim extends AbstractEntity 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Enfant::class, inversedBy="presenceculteecodims")
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity=Cultecodim::class, inversedBy="presenceculteecodims")
     */
    private $cultecodim;

    /**
     * @ORM\ManyToOne(targetEntity=Classecodim::class, inversedBy="presenceculteecodims")
     */
    private $classecodim;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="presenceculteecodims")
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCultecodim(): ?Cultecodim
    {
        return $this->cultecodim;
    }

    public function setCultecodim(?Cultecodim $cultecodim): self
    {
        $this->cultecodim = $cultecodim;

        return $this;
    }

    public function getClassecodim(): ?Classecodim
    {
        return $this->classecodim;
    }

    public function setClassecodim(?Classecodim $classecodim): self
    {
        $this->classecodim = $classecodim;

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
}
