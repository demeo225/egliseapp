<?php

namespace App\Entity;

use App\Repository\QuartierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuartierRepository::class)
 */
class Quartier extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("public")
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;



    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="quartiers")
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
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="quartier")
     */
    private $fidele;



    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="quartiers")
     */
    private $commune;

    /**
     * @ORM\OneToMany(targetEntity=Cellule::class, mappedBy="quartier")
     */
    private $cellules;

    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="quartier")
     */
    private $enfants;

  

  

 

    public function __construct()
    {
        $this->fidele = new ArrayCollection();
        $this->cellules = new ArrayCollection();
        $this->enfants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
    
    public function __toString() {
        return $this->getLibelle();;
    }

    /**
     * @return Collection|Fidele[]
     */
    public function getFidele(): Collection
    {
        return $this->fidele;
    }

    public function addFidele(Fidele $fidele): self
    {
        if (!$this->fidele->contains($fidele)) {
            $this->fidele[] = $fidele;
            $fidele->setQuartier($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fidele->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getQuartier() === $this) {
                $fidele->setQuartier(null);
            }
        }

        return $this;
    }


    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    /**
     * @return Collection<int, Cellule>
     */
    public function getCellules(): Collection
    {
        return $this->cellules;
    }

    public function addCellule(Cellule $cellule): self
    {
        if (!$this->cellules->contains($cellule)) {
            $this->cellules[] = $cellule;
            $cellule->setQuartier($this);
        }

        return $this;
    }

    public function removeCellule(Cellule $cellule): self
    {
        if ($this->cellules->removeElement($cellule)) {
            // set the owning side to null (unless already changed)
            if ($cellule->getQuartier() === $this) {
                $cellule->setQuartier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enfant>
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setQuartier($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getQuartier() === $this) {
                $enfant->setQuartier(null);
            }
        }

        return $this;
    }

 



}
