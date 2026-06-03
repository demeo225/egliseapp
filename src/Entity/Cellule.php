<?php

namespace App\Entity;

use App\Repository\CelluleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CelluleRepository::class)
 */
class Cellule extends AbstractEntity
{ 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups("public")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups("public")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups("public")
     */
    private $responsable1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups("public")
     */
    private $responsable2;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $lattitude;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     *  @Groups("public")
     */
    private $adresse;

  

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     *  @Groups("public")
     */
    private $jour;

    /**
     * @ORM\Column(type="time", nullable=true)
     *  @Groups("public")
     */
    private $heure;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     *  @Groups("public")
     */
    private $lieu;

   

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

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
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="cellule")
     */
    private $fideles;


    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="cellule")
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cellule")
     */
    private $eglise;


    /**
     * @ORM\OneToMany(targetEntity=Seancecellule::class, mappedBy="cellule")
     */
    private $seancecellules;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="cellules")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Quartier::class, inversedBy="cellules")
     */
    private $quartier;

  
    /**
     * @ORM\OneToMany(targetEntity=Cotisercellule::class, mappedBy="cellule")
     */
    private $cotisercellules;

    /**
     * @ORM\OneToMany(targetEntity=Presencecellule::class, mappedBy="cellule")
     */
    private $presencecellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationcellule::class, mappedBy="cellule")
     */
    private $cotisationcellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserparcellule::class, mappedBy="cellule")
     */
    private $cotiserparcellules;

    /**
     * @ORM\OneToMany(targetEntity=Detailparcellule::class, mappedBy="cellule")
     */
    private $detailparcellules;

    /**
     * @ORM\OneToMany(targetEntity=Solecellule::class, mappedBy="cellule")
     */
    private $solecellules;

    /**
     * @ORM\OneToMany(targetEntity=Depensecellule::class, mappedBy="cellule")
     */
    private $depensecellules;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="cellule")
     */
    private $users;

    public function __construct()
    {
        $this->fideles = new ArrayCollection();
        $this->enfant = new ArrayCollection();
        $this->seancecellules = new ArrayCollection();
        $this->cotisercellules = new ArrayCollection();
        $this->presencecellules = new ArrayCollection();
        $this->cotisationcellules = new ArrayCollection();
        $this->cotiserparcellules = new ArrayCollection();
        $this->detailparcellules = new ArrayCollection();
        $this->solecellules = new ArrayCollection();
        $this->depensecellules = new ArrayCollection();
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

    public function setNom(string $nom): self
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

    public function getLattitude(): ?string
    {
        return $this->lattitude;
    }

    public function setLattitude(?string $lattitude): self
    {
        $this->lattitude = $lattitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }



    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(?string $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(?\DateTimeInterface $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

 

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

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
      return  $this->getNom();
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
            $fidele->setCellule($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fideles->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getCellule() === $this) {
                $fidele->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfant(): Collection
    {
        return $this->enfant;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (!$this->enfant->contains($enfant)) {
            $this->enfant[] = $enfant;
            $enfant->setCellule($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfant->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getCellule() === $this) {
                $enfant->setCellule(null);
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
     * @return Collection|Seancecellule[]
     */
    public function getSeancecellules(): Collection
    {
        return $this->seancecellules;
    }

    public function addSeancecellule(Seancecellule $seancecellule): self
    {
        if (!$this->seancecellules->contains($seancecellule)) {
            $this->seancecellules[] = $seancecellule;
            $seancecellule->setCellule($this);
        }

        return $this;
    }

    public function removeSeancecellule(Seancecellule $seancecellule): self
    {
        if ($this->seancecellules->removeElement($seancecellule)) {
            // set the owning side to null (unless already changed)
            if ($seancecellule->getCellule() === $this) {
                $seancecellule->setCellule(null);
            }
        }

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

  

    /**
     * @return Collection<int, Cotisercellule>
     */
    public function getCotisercellules(): Collection
    {
        return $this->cotisercellules;
    }

    public function addCotisercellule(Cotisercellule $cotisercellule): self
    {
        if (!$this->cotisercellules->contains($cotisercellule)) {
            $this->cotisercellules[] = $cotisercellule;
            $cotisercellule->setCellule($this);
        }

        return $this;
    }

    public function removeCotisercellule(Cotisercellule $cotisercellule): self
    {
        if ($this->cotisercellules->removeElement($cotisercellule)) {
            // set the owning side to null (unless already changed)
            if ($cotisercellule->getCellule() === $this) {
                $cotisercellule->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencecellule>
     */
    public function getPresencecellules(): Collection
    {
        return $this->presencecellules;
    }

    public function addPresencecellule(Presencecellule $presencecellule): self
    {
        if (!$this->presencecellules->contains($presencecellule)) {
            $this->presencecellules[] = $presencecellule;
            $presencecellule->setCellule($this);
        }

        return $this;
    }

    public function removePresencecellule(Presencecellule $presencecellule): self
    {
        if ($this->presencecellules->removeElement($presencecellule)) {
            // set the owning side to null (unless already changed)
            if ($presencecellule->getCellule() === $this) {
                $presencecellule->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationcellule>
     */
    public function getCotisationcellules(): Collection
    {
        return $this->cotisationcellules;
    }

    public function addCotisationcellule(Cotisationcellule $cotisationcellule): self
    {
        if (!$this->cotisationcellules->contains($cotisationcellule)) {
            $this->cotisationcellules[] = $cotisationcellule;
            $cotisationcellule->setCellule($this);
        }

        return $this;
    }

    public function removeCotisationcellule(Cotisationcellule $cotisationcellule): self
    {
        if ($this->cotisationcellules->removeElement($cotisationcellule)) {
            // set the owning side to null (unless already changed)
            if ($cotisationcellule->getCellule() === $this) {
                $cotisationcellule->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserparcellule>
     */
    public function getCotiserparcellules(): Collection
    {
        return $this->cotiserparcellules;
    }

    public function addCotiserparcellule(Cotiserparcellule $cotiserparcellule): self
    {
        if (!$this->cotiserparcellules->contains($cotiserparcellule)) {
            $this->cotiserparcellules[] = $cotiserparcellule;
            $cotiserparcellule->setCellule($this);
        }

        return $this;
    }

    public function removeCotiserparcellule(Cotiserparcellule $cotiserparcellule): self
    {
        if ($this->cotiserparcellules->removeElement($cotiserparcellule)) {
            // set the owning side to null (unless already changed)
            if ($cotiserparcellule->getCellule() === $this) {
                $cotiserparcellule->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailparcellule>
     */
    public function getDetailparcellules(): Collection
    {
        return $this->detailparcellules;
    }

    public function addDetailparcellule(Detailparcellule $detailparcellule): self
    {
        if (!$this->detailparcellules->contains($detailparcellule)) {
            $this->detailparcellules[] = $detailparcellule;
            $detailparcellule->setCellule($this);
        }

        return $this;
    }

    public function removeDetailparcellule(Detailparcellule $detailparcellule): self
    {
        if ($this->detailparcellules->removeElement($detailparcellule)) {
            // set the owning side to null (unless already changed)
            if ($detailparcellule->getCellule() === $this) {
                $detailparcellule->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Solecellule>
     */
    public function getSolecellules(): Collection
    {
        return $this->solecellules;
    }

    public function addSolecellule(Solecellule $solecellule): self
    {
        if (!$this->solecellules->contains($solecellule)) {
            $this->solecellules[] = $solecellule;
            $solecellule->setCellule($this);
        }

        return $this;
    }

    public function removeSolecellule(Solecellule $solecellule): self
    {
        if ($this->solecellules->removeElement($solecellule)) {
            // set the owning side to null (unless already changed)
            if ($solecellule->getCellule() === $this) {
                $solecellule->setCellule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depensecellule>
     */
    public function getDepensecellules(): Collection
    {
        return $this->depensecellules;
    }

    public function addDepensecellule(Depensecellule $depensecellule): self
    {
        if (!$this->depensecellules->contains($depensecellule)) {
            $this->depensecellules[] = $depensecellule;
            $depensecellule->setCellule($this);
        }

        return $this;
    }

    public function removeDepensecellule(Depensecellule $depensecellule): self
    {
        if ($this->depensecellules->removeElement($depensecellule)) {
            // set the owning side to null (unless already changed)
            if ($depensecellule->getCellule() === $this) {
                $depensecellule->setCellule(null);
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
            $user->setCellule($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCellule() === $this) {
                $user->setCellule(null);
            }
        }

        return $this;
    }
}
