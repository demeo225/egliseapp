<?php

namespace App\Entity;

use App\Repository\FamilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=FamilleRepository::class)
 */
class Famille extends AbstractEntity
{ 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     *  @Groups("public")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups("public")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     *  @Groups("public")
     */
    private $responsable1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups("public")
     */
    private $responsable2;

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
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="famille")
     */
    private $fidele;

    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="famille")
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="famille")
     */
    private $eglise;



    /**
     * @ORM\OneToMany(targetEntity=Cotisationfamille::class, mappedBy="famille")
     */
    private $cotisationfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Presencefamille::class, mappedBy="famille")
     */
    private $presencefamilles;

    /**
     * @ORM\OneToMany(targetEntity=Seancefamille::class, mappedBy="famille")
     */
    private $seancefamilles;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="familles")
     */
    private $zone;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserparfamille::class, mappedBy="famille")
     */
    private $cotiserparfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Detailparfamille::class, mappedBy="famille")
     */
    private $detailparfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Depensefamille::class, mappedBy="famille")
     */
    private $depensefamilles;

    /**
     * @ORM\OneToMany(targetEntity=Soldefamille::class, mappedBy="famille")
     */
    private $soldefamilles;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="famille")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationfamille::class, mappedBy="famille")
     */
    private $detailcotisationfamilles;

    public function __construct()
    {
        $this->fidele = new ArrayCollection();
        $this->enfant = new ArrayCollection();
        $this->cotisationfamilles = new ArrayCollection();
        $this->presencefamilles = new ArrayCollection();
        $this->seancefamilles = new ArrayCollection();
        $this->cotiserparfamilles = new ArrayCollection();
        $this->detailparfamilles = new ArrayCollection();
        $this->depensefamilles = new ArrayCollection();
        $this->soldefamilles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->detailcotisationfamilles = new ArrayCollection();
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
    public function getFidele(): Collection
    {
        return $this->fidele;
    }

    public function addFidele(Fidele $fidele): self
    {
        if (!$this->fidele->contains($fidele)) {
            $this->fidele[] = $fidele;
            $fidele->setFamille($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fidele->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getFamille() === $this) {
                $fidele->setFamille(null);
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
            $enfant->setFamille($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfant->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getFamille() === $this) {
                $enfant->setFamille(null);
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
     * @return Collection<int, Cotisationfamille>
     */
    public function getCotisationfamilles(): Collection
    {
        return $this->cotisationfamilles;
    }

    public function addCotisationfamille(Cotisationfamille $cotisationfamille): self
    {
        if (!$this->cotisationfamilles->contains($cotisationfamille)) {
            $this->cotisationfamilles[] = $cotisationfamille;
            $cotisationfamille->setFamille($this);
        }

        return $this;
    }

    public function removeCotisationfamille(Cotisationfamille $cotisationfamille): self
    {
        if ($this->cotisationfamilles->removeElement($cotisationfamille)) {
            // set the owning side to null (unless already changed)
            if ($cotisationfamille->getFamille() === $this) {
                $cotisationfamille->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencefamille>
     */
    public function getPresencefamilles(): Collection
    {
        return $this->presencefamilles;
    }

    public function addPresencefamille(Presencefamille $presencefamille): self
    {
        if (!$this->presencefamilles->contains($presencefamille)) {
            $this->presencefamilles[] = $presencefamille;
            $presencefamille->setFamille($this);
        }

        return $this;
    }

    public function removePresencefamille(Presencefamille $presencefamille): self
    {
        if ($this->presencefamilles->removeElement($presencefamille)) {
            // set the owning side to null (unless already changed)
            if ($presencefamille->getFamille() === $this) {
                $presencefamille->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Seancefamille>
     */
    public function getSeancefamilles(): Collection
    {
        return $this->seancefamilles;
    }

    public function addSeancefamille(Seancefamille $seancefamille): self
    {
        if (!$this->seancefamilles->contains($seancefamille)) {
            $this->seancefamilles[] = $seancefamille;
            $seancefamille->setFamille($this);
        }

        return $this;
    }

    public function removeSeancefamille(Seancefamille $seancefamille): self
    {
        if ($this->seancefamilles->removeElement($seancefamille)) {
            // set the owning side to null (unless already changed)
            if ($seancefamille->getFamille() === $this) {
                $seancefamille->setFamille(null);
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

    /**
     * @return Collection<int, Cotiserparfamille>
     */
    public function getCotiserparfamilles(): Collection
    {
        return $this->cotiserparfamilles;
    }

    public function addCotiserparfamille(Cotiserparfamille $cotiserparfamille): self
    {
        if (!$this->cotiserparfamilles->contains($cotiserparfamille)) {
            $this->cotiserparfamilles[] = $cotiserparfamille;
            $cotiserparfamille->setFamille($this);
        }

        return $this;
    }

    public function removeCotiserparfamille(Cotiserparfamille $cotiserparfamille): self
    {
        if ($this->cotiserparfamilles->removeElement($cotiserparfamille)) {
            // set the owning side to null (unless already changed)
            if ($cotiserparfamille->getFamille() === $this) {
                $cotiserparfamille->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailparfamille>
     */
    public function getDetailparfamilles(): Collection
    {
        return $this->detailparfamilles;
    }

    public function addDetailparfamille(Detailparfamille $detailparfamille): self
    {
        if (!$this->detailparfamilles->contains($detailparfamille)) {
            $this->detailparfamilles[] = $detailparfamille;
            $detailparfamille->setFamille($this);
        }

        return $this;
    }

    public function removeDetailparfamille(Detailparfamille $detailparfamille): self
    {
        if ($this->detailparfamilles->removeElement($detailparfamille)) {
            // set the owning side to null (unless already changed)
            if ($detailparfamille->getFamille() === $this) {
                $detailparfamille->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depensefamille>
     */
    public function getDepensefamilles(): Collection
    {
        return $this->depensefamilles;
    }

    public function addDepensefamille(Depensefamille $depensefamille): self
    {
        if (!$this->depensefamilles->contains($depensefamille)) {
            $this->depensefamilles[] = $depensefamille;
            $depensefamille->setFamille($this);
        }

        return $this;
    }

    public function removeDepensefamille(Depensefamille $depensefamille): self
    {
        if ($this->depensefamilles->removeElement($depensefamille)) {
            // set the owning side to null (unless already changed)
            if ($depensefamille->getFamille() === $this) {
                $depensefamille->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Soldefamille>
     */
    public function getSoldefamilles(): Collection
    {
        return $this->soldefamilles;
    }

    public function addSoldefamille(Soldefamille $soldefamille): self
    {
        if (!$this->soldefamilles->contains($soldefamille)) {
            $this->soldefamilles[] = $soldefamille;
            $soldefamille->setFamille($this);
        }

        return $this;
    }

    public function removeSoldefamille(Soldefamille $soldefamille): self
    {
        if ($this->soldefamilles->removeElement($soldefamille)) {
            // set the owning side to null (unless already changed)
            if ($soldefamille->getFamille() === $this) {
                $soldefamille->setFamille(null);
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
            $user->setFamille($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getFamille() === $this) {
                $user->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisationfamille>
     */
    public function getDetailcotisationfamilles(): Collection
    {
        return $this->detailcotisationfamilles;
    }

    public function addDetailcotisationfamille(Detailcotisationfamille $detailcotisationfamille): self
    {
        if (!$this->detailcotisationfamilles->contains($detailcotisationfamille)) {
            $this->detailcotisationfamilles[] = $detailcotisationfamille;
            $detailcotisationfamille->setFamille($this);
        }

        return $this;
    }

    public function removeDetailcotisationfamille(Detailcotisationfamille $detailcotisationfamille): self
    {
        if ($this->detailcotisationfamilles->removeElement($detailcotisationfamille)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationfamille->getFamille() === $this) {
                $detailcotisationfamille->setFamille(null);
            }
        }

        return $this;
    }
}
