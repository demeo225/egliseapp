<?php

namespace App\Entity;

use App\Repository\DepensecelluleRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass=DepensecelluleRepository::class)
 */
class Depensecellule  extends AbstractEntity
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $detail;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="depensecellules")
     */
    private $cellule;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typeoff;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $ajout;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="depensecellules")
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

    public function getCellule(): ?Cellule
    {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self
    {
        $this->cellule = $cellule;

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

    public function getTypeoff(): ?bool
    {
        return $this->typeoff;
    }

    public function setTypeoff(?bool $typeoff): self
    {
        $this->typeoff = $typeoff;

        return $this;
    }

    public function getAjout(): ?string
    {
        return $this->ajout;
    }

    public function setAjout(?string $ajout): self
    {
        $this->ajout = $ajout;

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
