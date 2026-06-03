<?php

namespace App\Entity;

use App\Repository\CommuneRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=CommuneRepository::class)
 */
class Commune extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("public")
     */
    private $nom;

 
  
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
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="commune")
     */
    private $fideles;



    /**
     * @ORM\OneToMany(targetEntity=Quartier::class, mappedBy="commune")
     */
    private $quartiers;

    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="commune")
     */
    private $enfants;

   


    public function __construct()
    {
        $this->fideles = new ArrayCollection();
        $this->quartiers = new ArrayCollection();
        $this->enfants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }



    public function removeEglise(Eglise $eglise): self
    {
        if ($this->eglises->removeElement($eglise)) {
            // set the owning side to null (unless already changed)
            if ($eglise->getCommune() === $this) {
                $eglise->setCommune(null);
            }
        }

        return $this;
    }
//
//    /**
//     * @return Collection|Fidele[]
//     */
//    public function getFideles(): Collection
//    {
//        return $this->fideles;
//    }
//
//    public function addFidele(Fidele $fidele): self
//    {
//        if (!$this->fideles->contains($fidele)) {
//            $this->fideles[] = $fidele;
//            $fidele->setCommune($this);
//        }
//
//        return $this;
//    }
//
//    public function removeFidele(Fidele $fidele): self
//    {
//        if ($this->fideles->removeElement($fidele)) {
//            // set the owning side to null (unless already changed)
//            if ($fidele->getCommune() === $this) {
//                $fidele->setCommune(null);
//            }
//        }
//
//        return $this;
//    }
    public function __toString() {
        return $this->getNom();
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
    
    
    public function getCreateAt(): ?DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(?DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection|Fidele[]
     */
    public function getFideles(): Collection
    {
        return $this->fideles;
    }

    public function addFidele(Fidele $fidele): self
    {
        if (!$this->fideles->contains($fidele)) {
            $this->fideles[] = $fidele;
            $fidele->setCommune($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fideles->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getCommune() === $this) {
                $fidele->setCommune(null);
            }
        }

        return $this;
    }

    

    /**
     * @return Collection<int, Quartier>
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): self
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers[] = $quartier;
            $quartier->setCommune($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): self
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getCommune() === $this) {
                $quartier->setCommune(null);
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
            $enfant->setCommune($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getCommune() === $this) {
                $enfant->setCommune(null);
            }
        }

        return $this;
    }

 


}
