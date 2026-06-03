<?php

namespace App\Entity;

use App\Repository\DepensefamilleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=DepensefamilleRepository::class)
 */
class Depensefamille  extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datedepense;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objet;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typeoff;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ajout;

    /**
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="depensefamilles")
     */
    private $famille;
    
        
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
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="depensefamilles")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedepense(): ?\DateTimeInterface
    {
        return $this->datedepense;
    }

    public function setDatedepense(?\DateTimeInterface $datedepense): self
    {
        $this->datedepense = $datedepense;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(?string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getTypeoff(): ?bool
    {
        return $this->typeoff;
    }

    public function setTypeoff(?bool $typeoff): self
    {
        $this->typeoff = $typeoff;

        return $this;
    }

    public function getAjout(): ?int
    {
        return $this->ajout;
    }

    public function setAjout(?int $ajout): self
    {
        $this->ajout = $ajout;

        return $this;
    }

    public function getFamille(): ?Famille
    {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;

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
