<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DepartementRepository::class)
 */
class Departement extends AbstractEntity
{ 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     *  @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createAt;

    /**
     *  @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

   

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable2;

    /**
     * @ORM\OneToMany(targetEntity=Groupe::class, mappedBy="departement")
     */
    private $groupes;

    /**
     * @ORM\OneToMany(targetEntity=Departementfidele::class, mappedBy="departement")
     */
    private $departementfideles;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="departement")
     */
    private $eglise;

 
    /**
     * @ORM\OneToMany(targetEntity=Groupefidele::class, mappedBy="departement")
     */
    private $groupefideles;



    /**
     * @ORM\OneToMany(targetEntity=Presencedepartement::class, mappedBy="departement")
     */
    private $presencedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationdepartement::class, mappedBy="departement")
     */
    private $cotisationdepartements;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpardepartement::class, mappedBy="departement")
     */
    private $cotiserpardepartements;

    /**
     * @ORM\OneToMany(targetEntity=Detailpardepartement::class, mappedBy="departement")
     */
    private $detailpardepartements;

    /**
     * @ORM\OneToMany(targetEntity=Soldedepartement::class, mappedBy="departement")
     */
    private $soldedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Depensedepartement::class, mappedBy="departement")
     */
    private $depensedepartements;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="departement")
     */
    private $users;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->departementfideles = new ArrayCollection();
        $this->groupefideles = new ArrayCollection();
        $this->presencedepartements = new ArrayCollection();
        $this->cotisationdepartements = new ArrayCollection();
        $this->cotiserpardepartements = new ArrayCollection();
        $this->detailpardepartements = new ArrayCollection();
        $this->soldedepartements = new ArrayCollection();
        $this->depensedepartements = new ArrayCollection();
        $this->users = new ArrayCollection();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
 
    public function getResponsable1(): ?string
    {
        return $this->responsable1;
    }

    public function setResponsable1(?string $responsable1): self
    {
        $this->responsable1 = $responsable1;

        return $this;
    }

    public function getResponsable2(): ?string
    {
        return $this->responsable2;
    }

    public function setResponsable2(?string $responsable2): self
    {
        $this->responsable2 = $responsable2;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setDepartement($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getDepartement() === $this) {
                $groupe->setDepartement(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return  $this->getNom();
    }

    /**
     * @return Collection|Departementfidele[]
     */
    public function getDepartementfideles(): Collection
    {
        return $this->departementfideles;
    }

    public function addDepartementfidele(Departementfidele $departementfidele): self
    {
        if (!$this->departementfideles->contains($departementfidele)) {
            $this->departementfideles[] = $departementfidele;
            $departementfidele->setDepartement($this);
        }

        return $this;
    }

    public function removeDepartementfidele(Departementfidele $departementfidele): self
    {
        if ($this->departementfideles->removeElement($departementfidele)) {
            // set the owning side to null (unless already changed)
            if ($departementfidele->getDepartement() === $this) {
                $departementfidele->setDepartement(null);
            }
        }

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

 

    /**
     * @return Collection|Groupefidele[]
     */
    public function getGroupefideles(): Collection
    {
        return $this->groupefideles;
    }

    public function addGroupefidele(Groupefidele $groupefidele): self
    {
        if (!$this->groupefideles->contains($groupefidele)) {
            $this->groupefideles[] = $groupefidele;
            $groupefidele->setDepartement($this);
        }

        return $this;
    }

    public function removeGroupefidele(Groupefidele $groupefidele): self
    {
        if ($this->groupefideles->removeElement($groupefidele)) {
            // set the owning side to null (unless already changed)
            if ($groupefidele->getDepartement() === $this) {
                $groupefidele->setDepartement(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Presencedepartement>
     */
    public function getPresencedepartements(): Collection
    {
        return $this->presencedepartements;
    }

    public function addPresencedepartement(Presencedepartement $presencedepartement): self
    {
        if (!$this->presencedepartements->contains($presencedepartement)) {
            $this->presencedepartements[] = $presencedepartement;
            $presencedepartement->setDepartement($this);
        }

        return $this;
    }

    public function removePresencedepartement(Presencedepartement $presencedepartement): self
    {
        if ($this->presencedepartements->removeElement($presencedepartement)) {
            // set the owning side to null (unless already changed)
            if ($presencedepartement->getDepartement() === $this) {
                $presencedepartement->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationdepartement>
     */
    public function getCotisationdepartements(): Collection
    {
        return $this->cotisationdepartements;
    }

    public function addCotisationdepartement(Cotisationdepartement $cotisationdepartement): self
    {
        if (!$this->cotisationdepartements->contains($cotisationdepartement)) {
            $this->cotisationdepartements[] = $cotisationdepartement;
            $cotisationdepartement->setDepartement($this);
        }

        return $this;
    }

    public function removeCotisationdepartement(Cotisationdepartement $cotisationdepartement): self
    {
        if ($this->cotisationdepartements->removeElement($cotisationdepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotisationdepartement->getDepartement() === $this) {
                $cotisationdepartement->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserpardepartement>
     */
    public function getCotiserpardepartements(): Collection
    {
        return $this->cotiserpardepartements;
    }

    public function addCotiserpardepartement(Cotiserpardepartement $cotiserpardepartement): self
    {
        if (!$this->cotiserpardepartements->contains($cotiserpardepartement)) {
            $this->cotiserpardepartements[] = $cotiserpardepartement;
            $cotiserpardepartement->setDepartement($this);
        }

        return $this;
    }

    public function removeCotiserpardepartement(Cotiserpardepartement $cotiserpardepartement): self
    {
        if ($this->cotiserpardepartements->removeElement($cotiserpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpardepartement->getDepartement() === $this) {
                $cotiserpardepartement->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailpardepartement>
     */
    public function getDetailpardepartements(): Collection
    {
        return $this->detailpardepartements;
    }

    public function addDetailpardepartement(Detailpardepartement $detailpardepartement): self
    {
        if (!$this->detailpardepartements->contains($detailpardepartement)) {
            $this->detailpardepartements[] = $detailpardepartement;
            $detailpardepartement->setDepartement($this);
        }

        return $this;
    }

    public function removeDetailpardepartement(Detailpardepartement $detailpardepartement): self
    {
        if ($this->detailpardepartements->removeElement($detailpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($detailpardepartement->getDepartement() === $this) {
                $detailpardepartement->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Soldedepartement>
     */
    public function getSoldedepartements(): Collection
    {
        return $this->soldedepartements;
    }

    public function addSoldedepartement(Soldedepartement $soldedepartement): self
    {
        if (!$this->soldedepartements->contains($soldedepartement)) {
            $this->soldedepartements[] = $soldedepartement;
            $soldedepartement->setDepartemet($this);
        }

        return $this;
    }

    public function removeSoldedepartement(Soldedepartement $soldedepartement): self
    {
        if ($this->soldedepartements->removeElement($soldedepartement)) {
            // set the owning side to null (unless already changed)
            if ($soldedepartement->getDepartemet() === $this) {
                $soldedepartement->setDepartemet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depensedepartement>
     */
    public function getDepensedepartements(): Collection
    {
        return $this->depensedepartements;
    }

    public function addDepensedepartement(Depensedepartement $depensedepartement): self
    {
        if (!$this->depensedepartements->contains($depensedepartement)) {
            $this->depensedepartements[] = $depensedepartement;
            $depensedepartement->setDepartement($this);
        }

        return $this;
    }

    public function removeDepensedepartement(Depensedepartement $depensedepartement): self
    {
        if ($this->depensedepartements->removeElement($depensedepartement)) {
            // set the owning side to null (unless already changed)
            if ($depensedepartement->getDepartement() === $this) {
                $depensedepartement->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setDepartement($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getDepartement() === $this) {
                $user->setDepartement(null);
            }
        }

        return $this;
    }
}
