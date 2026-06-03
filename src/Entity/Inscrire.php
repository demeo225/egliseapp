<?php

namespace App\Entity;

use App\Repository\InscrireRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InscrireRepository::class)
 */
class Inscrire extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $dateinscrire;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatinscrire;

    /**
     * @ORM\ManyToOne(targetEntity=Enfant::class, inversedBy="inscrires")
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity=Classecodim::class, inversedBy="inscrires")
     */
    private $classecodim;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datefin;

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
     */
    private $raisondelete;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="inscrires")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateinscrire(): ?\DateTimeInterface
    {
        return $this->dateinscrire;
    }

    public function setDateinscrire(?\DateTimeInterface $dateinscrire): self
    {
        $this->dateinscrire = $dateinscrire;

        return $this;
    }

    public function getEtatinscrire(): ?bool
    {
        return $this->etatinscrire;
    }

    public function setEtatinscrire(?bool $etatinscrire): self
    {
        $this->etatinscrire = $etatinscrire;

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

    public function getClassecodim(): ?Classecodim
    {
        return $this->classecodim;
    }

    public function setClassecodim(?Classecodim $classecodim): self
    {
        $this->classecodim = $classecodim;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

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

    public function getRaisondelete(): ?string
    {
        return $this->raisondelete;
    }

    public function setRaisondelete(?string $raisondelete): self
    {
        $this->raisondelete = $raisondelete;

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
}
