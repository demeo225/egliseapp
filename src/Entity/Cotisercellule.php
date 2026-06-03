<?php

namespace App\Entity;

use App\Repository\CotisercelluleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass=CotisercelluleRepository::class)
 */
class Cotisercellule extends AbstractEntity
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
     * @Assert\NotBlank()
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
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="cotisercellules")
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Cotisationcellule::class, inversedBy="cotisercellules")
     * @Assert\NotBlank()
     */
    private $cotisationcellule;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecotiser;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationcellule::class, mappedBy="cotisercellule")
     */
    private $detailcotisationcellules;



    public function __construct()
    {
        $this->detailcotisationcellules = new ArrayCollection();
    }

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

    public function getCellule(): ?Cellule
    {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self
    {
        $this->cellule = $cellule;

        return $this;
    }

    public function getCotisationcellule(): ?Cotisationcellule
    {
        return $this->cotisationcellule;
    }

    public function setCotisationcellule(?Cotisationcellule $cotisationcellule): self
    {
        $this->cotisationcellule = $cotisationcellule;

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

    /**
     * @return Collection<int, Detailcotisationcellule>
     */
    public function getDetailcotisationcellules(): Collection
    {
        return $this->detailcotisationcellules;
    }

    public function addDetailcotisationcellule(Detailcotisationcellule $detailcotisationcellule): self
    {
        if (!$this->detailcotisationcellules->contains($detailcotisationcellule)) {
            $this->detailcotisationcellules[] = $detailcotisationcellule;
            $detailcotisationcellule->setCotisercellule($this);
        }

        return $this;
    }

    public function removeDetailcotisationcellule(Detailcotisationcellule $detailcotisationcellule): self
    {
        if ($this->detailcotisationcellules->removeElement($detailcotisationcellule)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationcellule->getCotisercellule() === $this) {
                $detailcotisationcellule->setCotisercellule(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->montantpayer;
    }
}
