<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GroupeRepository::class)
 */
class Groupe extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="groupes")
     */
    private $departement;

    /**
     * @ORM\OneToMany(targetEntity=Groupefidele::class, mappedBy="groupe")
     */
    private $groupefideles;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="groupe")
     */
    private $eglise;


    /**
     * @ORM\OneToMany(targetEntity=Cotisationgroupe::class, mappedBy="groupe")
     */
    private $cotisationgroupes;

    /**
     * @ORM\OneToMany(targetEntity=Presencegroupe::class, mappedBy="groupe")
     */
    private $presencegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpargroupe::class, mappedBy="groupe")
     */
    private $cotiserpargroupes;

    /**
     * @ORM\OneToMany(targetEntity=Detailpargroupe::class, mappedBy="groupe")
     */
    private $detailpargroupes;

    /**
     * @ORM\OneToMany(targetEntity=Soldegroupe::class, mappedBy="groupe")
     */
    private $soldegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Depensegroupe::class, mappedBy="groupe")
     */
    private $depensegroupes;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="groupe")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Invitegroupe::class, mappedBy="groupe")
     */
    private $invitegroupes;

    public function __construct()
    {
        $this->groupefideles = new ArrayCollection();
        $this->cotisationgroupes = new ArrayCollection();
        $this->presencegroupes = new ArrayCollection();
        $this->cotiserpargroupes = new ArrayCollection();
        $this->detailpargroupes = new ArrayCollection();
        $this->soldegroupes = new ArrayCollection();
        $this->depensegroupes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->invitegroupes = new ArrayCollection();
     
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
        return $this->getNom();;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

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
            $groupefidele->setGroupe($this);
        }

        return $this;
    }

    public function removeGroupefidele(Groupefidele $groupefidele): self
    {
        if ($this->groupefideles->removeElement($groupefidele)) {
            // set the owning side to null (unless already changed)
            if ($groupefidele->getGroupe() === $this) {
                $groupefidele->setGroupe(null);
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
     * @return Collection<int, Cotisationgroupe>
     */
    public function getCotisationgroupes(): Collection
    {
        return $this->cotisationgroupes;
    }

    public function addCotisationgroupe(Cotisationgroupe $cotisationgroupe): self
    {
        if (!$this->cotisationgroupes->contains($cotisationgroupe)) {
            $this->cotisationgroupes[] = $cotisationgroupe;
            $cotisationgroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeCotisationgroupe(Cotisationgroupe $cotisationgroupe): self
    {
        if ($this->cotisationgroupes->removeElement($cotisationgroupe)) {
            // set the owning side to null (unless already changed)
            if ($cotisationgroupe->getGroupe() === $this) {
                $cotisationgroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencegroupe>
     */
    public function getPresencegroupes(): Collection
    {
        return $this->presencegroupes;
    }

    public function addPresencegroupe(Presencegroupe $presencegroupe): self
    {
        if (!$this->presencegroupes->contains($presencegroupe)) {
            $this->presencegroupes[] = $presencegroupe;
            $presencegroupe->setGroupe($this);
        }

        return $this;
    }

    public function removePresencegroupe(Presencegroupe $presencegroupe): self
    {
        if ($this->presencegroupes->removeElement($presencegroupe)) {
            // set the owning side to null (unless already changed)
            if ($presencegroupe->getGroupe() === $this) {
                $presencegroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserpargroupe>
     */
    public function getCotiserpargroupes(): Collection
    {
        return $this->cotiserpargroupes;
    }

    public function addCotiserpargroupe(Cotiserpargroupe $cotiserpargroupe): self
    {
        if (!$this->cotiserpargroupes->contains($cotiserpargroupe)) {
            $this->cotiserpargroupes[] = $cotiserpargroupe;
            $cotiserpargroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeCotiserpargroupe(Cotiserpargroupe $cotiserpargroupe): self
    {
        if ($this->cotiserpargroupes->removeElement($cotiserpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpargroupe->getGroupe() === $this) {
                $cotiserpargroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailpargroupe>
     */
    public function getDetailpargroupes(): Collection
    {
        return $this->detailpargroupes;
    }

    public function addDetailpargroupe(Detailpargroupe $detailpargroupe): self
    {
        if (!$this->detailpargroupes->contains($detailpargroupe)) {
            $this->detailpargroupes[] = $detailpargroupe;
            $detailpargroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeDetailpargroupe(Detailpargroupe $detailpargroupe): self
    {
        if ($this->detailpargroupes->removeElement($detailpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($detailpargroupe->getGroupe() === $this) {
                $detailpargroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Soldegroupe>
     */
    public function getSoldegroupes(): Collection
    {
        return $this->soldegroupes;
    }

    public function addSoldegroupe(Soldegroupe $soldegroupe): self
    {
        if (!$this->soldegroupes->contains($soldegroupe)) {
            $this->soldegroupes[] = $soldegroupe;
            $soldegroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeSoldegroupe(Soldegroupe $soldegroupe): self
    {
        if ($this->soldegroupes->removeElement($soldegroupe)) {
            // set the owning side to null (unless already changed)
            if ($soldegroupe->getGroupe() === $this) {
                $soldegroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depensegroupe>
     */
    public function getDepensegroupes(): Collection
    {
        return $this->depensegroupes;
    }

    public function addDepensegroupe(Depensegroupe $depensegroupe): self
    {
        if (!$this->depensegroupes->contains($depensegroupe)) {
            $this->depensegroupes[] = $depensegroupe;
            $depensegroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeDepensegroupe(Depensegroupe $depensegroupe): self
    {
        if ($this->depensegroupes->removeElement($depensegroupe)) {
            // set the owning side to null (unless already changed)
            if ($depensegroupe->getGroupe() === $this) {
                $depensegroupe->setGroupe(null);
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
            $user->setGroupe($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroupe() === $this) {
                $user->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitegroupe>
     */
    public function getInvitegroupes(): Collection
    {
        return $this->invitegroupes;
    }

    public function addInvitegroupe(Invitegroupe $invitegroupe): self
    {
        if (!$this->invitegroupes->contains($invitegroupe)) {
            $this->invitegroupes[] = $invitegroupe;
            $invitegroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeInvitegroupe(Invitegroupe $invitegroupe): self
    {
        if ($this->invitegroupes->removeElement($invitegroupe)) {
            // set the owning side to null (unless already changed)
            if ($invitegroupe->getGroupe() === $this) {
                $invitegroupe->setGroupe(null);
            }
        }

        return $this;
    }
}
