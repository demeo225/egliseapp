<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\ZoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ZoneRepository::class)
 */
class Zone extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responsable2;

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
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="zone")
     */
    private $fideles;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="zone")
     */
    private $eglise;


  
    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="zone")
     */
    private $enfants;

    /**
     * @ORM\OneToMany(targetEntity=Cellule::class, mappedBy="zone")
     */
    private $cellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserzone::class, mappedBy="zone")
     */
    private $cotiserzones;

   

    /**
     * @ORM\OneToMany(targetEntity=Seancezone::class, mappedBy="zone")
     */
    private $seancezones;

  
    /**
     * @ORM\OneToMany(targetEntity=Cotisationzone::class, mappedBy="zone")
     */
    private $cotisationzones;

    /**
     * @ORM\OneToMany(targetEntity=Zonecotisation::class, mappedBy="zone")
     */
    private $zonecotisations;

    /**
     * @ORM\OneToMany(targetEntity=Presencezone::class, mappedBy="zone")
     */
    private $presencezones;

    /**
     * @ORM\OneToMany(targetEntity=Famille::class, mappedBy="zone")
     */
    private $familles;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpazone::class, mappedBy="zone")
     */
    private $cotiserpazones;

    /**
     * @ORM\OneToMany(targetEntity=Detailparzone::class, mappedBy="zone")
     */
    private $detailparzones;

    /**
     * @ORM\OneToMany(targetEntity=Soldezone::class, mappedBy="zone")
     */
    private $soldezones;

    /**
     * @ORM\OneToMany(targetEntity=Depensezone::class, mappedBy="zone")
     */
    private $depensezones;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationzone::class, mappedBy="zone")
     */
    private $detailcotisationzones;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="zone")
     */
    private $users;

 


    public function __construct() {
        $this->fideles = new ArrayCollection();
        $this->enfants = new ArrayCollection();
        $this->cellules = new ArrayCollection();
        $this->cotiserzones = new ArrayCollection();
        $this->seancezones = new ArrayCollection();
        $this->cotisationzones = new ArrayCollection();
        $this->zonecotisations = new ArrayCollection();
        $this->presencezones = new ArrayCollection();
        $this->familles = new ArrayCollection();
        $this->cotiserpazones = new ArrayCollection();
        $this->detailparzones = new ArrayCollection();
        $this->soldezones = new ArrayCollection();
        $this->depensezones = new ArrayCollection();
   
        $this->detailcotisationzones = new ArrayCollection();
        $this->users = new ArrayCollection();
     
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getResponsable1(): ?string {
        return $this->responsable1;
    }

    public function setResponsable1(?string $responsable1): self {
        $this->responsable1 = $responsable1;

        return $this;
    }

    public function getResponsable2(): ?string {
        return $this->responsable2;
    }

    public function setResponsable2(?string $responsable2): self {
        $this->responsable2 = $responsable2;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): self {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function __toString() {
        return $this->getNom();
    }

    /**
     * @return Collection|Fidele[]
     */
    public function getFideles(): Collection {
        return $this->fideles;
    }

    public function addFidele(Fidele $fidele): self {
        if (!$this->fideles->contains($fidele)) {
            $this->fideles[] = $fidele;
            $fidele->setZone($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self {
        if ($this->fideles->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getZone() === $this) {
                $fidele->setZone(null);
            }
        }

        return $this;
    }

    public function getEglise(): ?Eglise {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self {
        $this->eglise = $eglise;

        return $this;
    }

 

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfants(): Collection {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setZone($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self {
        if ($this->enfants->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getZone() === $this) {
                $enfant->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cellule[]
     */
    public function getCellules(): Collection
    {
        return $this->cellules;
    }

    public function addCellule(Cellule $cellule): self
    {
        if (!$this->cellules->contains($cellule)) {
            $this->cellules[] = $cellule;
            $cellule->setZone($this);
        }

        return $this;
    }

    public function removeCellule(Cellule $cellule): self
    {
        if ($this->cellules->removeElement($cellule)) {
            // set the owning side to null (unless already changed)
            if ($cellule->getZone() === $this) {
                $cellule->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserzone>
     */
    public function getCotiserzones(): Collection
    {
        return $this->cotiserzones;
    }

    public function addCotiserzone(Cotiserzone $cotiserzone): self
    {
        if (!$this->cotiserzones->contains($cotiserzone)) {
            $this->cotiserzones[] = $cotiserzone;
            $cotiserzone->setZone($this);
        }

        return $this;
    }

    public function removeCotiserzone(Cotiserzone $cotiserzone): self
    {
        if ($this->cotiserzones->removeElement($cotiserzone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserzone->getZone() === $this) {
                $cotiserzone->setZone(null);
            }
        }

        return $this;
    }

   
    /**
     * @return Collection<int, Seancezone>
     */
    public function getSeancezones(): Collection
    {
        return $this->seancezones;
    }

    public function addSeancezone(Seancezone $seancezone): self
    {
        if (!$this->seancezones->contains($seancezone)) {
            $this->seancezones[] = $seancezone;
            $seancezone->setZone($this);
        }

        return $this;
    }

    public function removeSeancezone(Seancezone $seancezone): self
    {
        if ($this->seancezones->removeElement($seancezone)) {
            // set the owning side to null (unless already changed)
            if ($seancezone->getZone() === $this) {
                $seancezone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationzone>
     */
    public function getCotisationzones(): Collection
    {
        return $this->cotisationzones;
    }

    public function addCotisationzone(Cotisationzone $cotisationzone): self
    {
        if (!$this->cotisationzones->contains($cotisationzone)) {
            $this->cotisationzones[] = $cotisationzone;
            $cotisationzone->setZone($this);
        }

        return $this;
    }

    public function removeCotisationzone(Cotisationzone $cotisationzone): self
    {
        if ($this->cotisationzones->removeElement($cotisationzone)) {
            // set the owning side to null (unless already changed)
            if ($cotisationzone->getZone() === $this) {
                $cotisationzone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Zonecotisation>
     */
    public function getZonecotisations(): Collection
    {
        return $this->zonecotisations;
    }

    public function addZonecotisation(Zonecotisation $zonecotisation): self
    {
        if (!$this->zonecotisations->contains($zonecotisation)) {
            $this->zonecotisations[] = $zonecotisation;
            $zonecotisation->setZone($this);
        }

        return $this;
    }

    public function removeZonecotisation(Zonecotisation $zonecotisation): self
    {
        if ($this->zonecotisations->removeElement($zonecotisation)) {
            // set the owning side to null (unless already changed)
            if ($zonecotisation->getZone() === $this) {
                $zonecotisation->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencezone>
     */
    public function getPresencezones(): Collection
    {
        return $this->presencezones;
    }

    public function addPresencezone(Presencezone $presencezone): self
    {
        if (!$this->presencezones->contains($presencezone)) {
            $this->presencezones[] = $presencezone;
            $presencezone->setZone($this);
        }

        return $this;
    }

    public function removePresencezone(Presencezone $presencezone): self
    {
        if ($this->presencezones->removeElement($presencezone)) {
            // set the owning side to null (unless already changed)
            if ($presencezone->getZone() === $this) {
                $presencezone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Famille>
     */
    public function getFamilles(): Collection
    {
        return $this->familles;
    }

    public function addFamille(Famille $famille): self
    {
        if (!$this->familles->contains($famille)) {
            $this->familles[] = $famille;
            $famille->setZone($this);
        }

        return $this;
    }

    public function removeFamille(Famille $famille): self
    {
        if ($this->familles->removeElement($famille)) {
            // set the owning side to null (unless already changed)
            if ($famille->getZone() === $this) {
                $famille->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserpazone>
     */
    public function getCotiserpazones(): Collection
    {
        return $this->cotiserpazones;
    }

    public function addCotiserpazone(Cotiserpazone $cotiserpazone): self
    {
        if (!$this->cotiserpazones->contains($cotiserpazone)) {
            $this->cotiserpazones[] = $cotiserpazone;
            $cotiserpazone->setZone($this);
        }

        return $this;
    }

    public function removeCotiserpazone(Cotiserpazone $cotiserpazone): self
    {
        if ($this->cotiserpazones->removeElement($cotiserpazone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpazone->getZone() === $this) {
                $cotiserpazone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailparzone>
     */
    public function getDetailparzones(): Collection
    {
        return $this->detailparzones;
    }

    public function addDetailparzone(Detailparzone $detailparzone): self
    {
        if (!$this->detailparzones->contains($detailparzone)) {
            $this->detailparzones[] = $detailparzone;
            $detailparzone->setZone($this);
        }

        return $this;
    }

    public function removeDetailparzone(Detailparzone $detailparzone): self
    {
        if ($this->detailparzones->removeElement($detailparzone)) {
            // set the owning side to null (unless already changed)
            if ($detailparzone->getZone() === $this) {
                $detailparzone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Soldezone>
     */
    public function getSoldezones(): Collection
    {
        return $this->soldezones;
    }

    public function addSoldezone(Soldezone $soldezone): self
    {
        if (!$this->soldezones->contains($soldezone)) {
            $this->soldezones[] = $soldezone;
            $soldezone->setZone($this);
        }

        return $this;
    }

    public function removeSoldezone(Soldezone $soldezone): self
    {
        if ($this->soldezones->removeElement($soldezone)) {
            // set the owning side to null (unless already changed)
            if ($soldezone->getZone() === $this) {
                $soldezone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depensezone>
     */
    public function getDepensezones(): Collection
    {
        return $this->depensezones;
    }

    public function addDepensezone(Depensezone $depensezone): self
    {
        if (!$this->depensezones->contains($depensezone)) {
            $this->depensezones[] = $depensezone;
            $depensezone->setZone($this);
        }

        return $this;
    }

    public function removeDepensezone(Depensezone $depensezone): self
    {
        if ($this->depensezones->removeElement($depensezone)) {
            // set the owning side to null (unless already changed)
            if ($depensezone->getZone() === $this) {
                $depensezone->setZone(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisationzone>
     */
    public function getDetailcotisationzones(): Collection
    {
        return $this->detailcotisationzones;
    }

    public function addDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self
    {
        if (!$this->detailcotisationzones->contains($detailcotisationzone)) {
            $this->detailcotisationzones[] = $detailcotisationzone;
            $detailcotisationzone->setZone($this);
        }

        return $this;
    }

    public function removeDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self
    {
        if ($this->detailcotisationzones->removeElement($detailcotisationzone)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationzone->getZone() === $this) {
                $detailcotisationzone->setZone(null);
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
            $user->setZone($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getZone() === $this) {
                $user->setZone(null);
            }
        }

        return $this;
    }


}
