<?php

namespace App\Entity;

use App\Repository\EgliseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EgliseRepository::class)
 * @UniqueEntity(fields={"code"}, message="Ce code a déjà été utilisé")
 */
class Eglise extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

 
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $denomination;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $contact1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contact2;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quartier;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $verset;

    /**
     * @ORM\Column(type="string", length=225, nullable=true)
     */
    private $texte;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $sigle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $agrement;

    

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etateglise;

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
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="eglise")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Quartier::class, mappedBy="eglise")
     */
    private $quartiers;

    /**
     * @ORM\OneToMany(targetEntity=Inscrire::class, mappedBy="eglise")
     */
    private $inscrires;

    /**
     * @ORM\OneToMany(targetEntity=Dime::class, mappedBy="eglise")
     */
    private $dime;

    /**
     * @ORM\OneToMany(targetEntity=Cellule::class, mappedBy="eglise")
     */
    private $cellule;

    /**
     * @ORM\OneToMany(targetEntity=Classecodim::class, mappedBy="eglise")
     */
    private $classecodim;

    /**
     * @ORM\OneToMany(targetEntity=Cotisation::class, mappedBy="eglise")
     */
    private $cotisation;

    /**
     * @ORM\OneToMany(targetEntity=Culte::class, mappedBy="eglise")
     */
    private $culte;

    /**
     * @ORM\OneToMany(targetEntity=Cultecodim::class, mappedBy="eglise")
     */
    private $cultecodim;

    /**
     * @ORM\OneToMany(targetEntity=Departement::class, mappedBy="eglise")
     */
    private $departement;

    /**
     * @ORM\OneToMany(targetEntity=Departementfidele::class, mappedBy="eglise")
     */
    private $departementfidele;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="eglise")
     */
    private $evenement;

    /**
     * @ORM\OneToMany(targetEntity=Famille::class, mappedBy="eglise")
     */
    private $famille;

    /**
     * @ORM\OneToMany(targetEntity=Groupe::class, mappedBy="eglise")
     */
    private $groupe;

    /**
     * @ORM\OneToMany(targetEntity=Groupefidele::class, mappedBy="eglise")
     */
    private $groupefidele;

    /**
     * @ORM\OneToMany(targetEntity=Offrande::class, mappedBy="eglise")
     */
    private $offrande;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="eglise")
     */
    private $operation;
//
//    /**
//     * @ORM\OneToMany(targetEntity=Parentenfant::class, mappedBy="eglise")
//     */
//    private $parentenfant;

    /**
     * @ORM\OneToMany(targetEntity=Patrimoine::class, mappedBy="eglise")
     */
    private $patrimoine;

    /**
     * @ORM\OneToMany(targetEntity=Projet::class, mappedBy="eglise")
     */
    private $projet;

    /**
     * @ORM\OneToMany(targetEntity=Zone::class, mappedBy="eglise")
     */
    private $zone;

    /**
     * @ORM\OneToMany(targetEntity=Fidelecotiser::class, mappedBy="eglise")
     */
    private $fidelecotiser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

  
    /**
     * @ORM\OneToMany(targetEntity=Sms::class, mappedBy="eglise")
     */
    private $sms;

    /**
     * @ORM\OneToMany(targetEntity=Bapteme::class, mappedBy="eglise")
     */
    private $bapteme;

   

    /**
     * @ORM\OneToMany(targetEntity=Mariage::class, mappedBy="eglise")
     */
    private $mariage;

    /**
     * @ORM\OneToMany(targetEntity=Fiancaille::class, mappedBy="eglise")
     */
    private $fiancaille;

    /**
     * @ORM\OneToMany(targetEntity=Naissance::class, mappedBy="eglise")
     */
    private $naissance;

    /**
     * @ORM\OneToMany(targetEntity=Recommandation::class, mappedBy="eglise")
     */
    private $recommandation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $arrete;

    /**
     * @ORM\ManyToOne(targetEntity=Communaute::class, inversedBy="eglises")
     */
    private $communaute;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $commune;
 
     /**
     * @ORM\Column(type="string", length=128, nullable=true, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $congregation;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $administrateur;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $annee;

    /**
     * @ORM\OneToMany(targetEntity=Enfantactivite::class, mappedBy="eglise")
     */
    private $enfantactivites;

    /**
     * @ORM\OneToMany(targetEntity=Couple::class, mappedBy="eglise")
     */
    private $couples;

    /**
     * @ORM\OneToMany(targetEntity=Deces::class, mappedBy="eglise")
     */
    private $deces;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserdepartement::class, mappedBy="eglise")
     */
    private $cotiserdepartements;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserzone::class, mappedBy="eglise")
     */
    private $cotiserzones;

 

    /**
     * @ORM\OneToMany(targetEntity=Seancezone::class, mappedBy="eglise")
     */
    private $seancezones;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationzone::class, mappedBy="eglise")
     */
    private $cotisationzones;

    /**
     * @ORM\OneToMany(targetEntity=Zonecotisation::class, mappedBy="eglise")
     */
    private $zonecotisations;

    /**
     * @ORM\OneToMany(targetEntity=Presencecellule::class, mappedBy="eglise")
     */
    private $presencecellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationcellule::class, mappedBy="eglise")
     */
    private $cotisationcellules;

    /**
     * @ORM\OneToMany(targetEntity=Presencegroupe::class, mappedBy="eglise")
     */
    private $presencegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Presencedepartement::class, mappedBy="eglise")
     */
    private $presencedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Presencezone::class, mappedBy="eglise")
     */
    private $presencezones;

    /**
     * @ORM\OneToMany(targetEntity=Presencefamille::class, mappedBy="eglise")
     */
    private $presencefamilles;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisation::class, mappedBy="eglise")
     */
    private $detailcotisations;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationzone::class, mappedBy="eglise")
     */
    private $detailcotisationzones;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationsociale::class, mappedBy="eglise")
     */
    private $cotisationsociales;

    /**
     * @ORM\OneToMany(targetEntity=Paiement::class, mappedBy="eglise")
     */
    private $paiements;

    /**
     * @ORM\OneToMany(targetEntity=Detailenfantactivite::class, mappedBy="eglise")
     */
    private $detailenfantactivites;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted2At;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationparcellule::class, mappedBy="eglise")
     */
    private $cotisationparcellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationpardepartement::class, mappedBy="eglise")
     */
    private $cotisationpardepartements;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationparfamille::class, mappedBy="eglise")
     */
    private $cotisationparfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationpargroupe::class, mappedBy="eglise")
     */
    private $cotisationpargroupes;

    /**
     * @ORM\OneToMany(targetEntity=Cotisationparzone::class, mappedBy="eglise")
     */
    private $cotisationparzones;


    /**
     * @ORM\OneToMany(targetEntity=Cotiserpardepartement::class, mappedBy="eglise")
     */
    private $cotiserpardepartements;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserparcellule::class, mappedBy="eglise")
     */
    private $cotiserparcellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserparfamille::class, mappedBy="eglise")
     */
    private $cotiserparfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpargroupe::class, mappedBy="eglise")
     */
    private $cotiserpargroupes;

  

    /**
     * @ORM\OneToMany(targetEntity=Detailparzone::class, mappedBy="eglise")
     */
    private $detailparzones;

    /**
     * @ORM\OneToMany(targetEntity=Detailpardepartement::class, mappedBy="eglise")
     */
    private $detailpardepartements;

    /**
     * @ORM\OneToMany(targetEntity=Detailpargroupe::class, mappedBy="eglise")
     */
    private $detailpargroupes;

    /**
     * @ORM\OneToMany(targetEntity=Detailparfamille::class, mappedBy="eglise")
     */
    private $detailparfamilles;

    /**
     * @ORM\OneToMany(targetEntity=Detailparcellule::class, mappedBy="eglise")
     */
    private $detailparcellules;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserpazone::class, mappedBy="eglise")
     */
    private $cotiserpazones;

    /**
     * @ORM\OneToMany(targetEntity=Presenceculteecodim::class, mappedBy="eglise")
     */
    private $presenceculteecodims;

    /**
     * @ORM\OneToMany(targetEntity=Visite::class, mappedBy="eglise")
     */
    private $visites;

   

    /**
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="eglise")
     */
    private $fideles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="eglises")
     */
    private $region;



    /**
     * @ORM\OneToMany(targetEntity=Solde::class, mappedBy="eglise")
     */
    private $soldes;

    /**
     * @ORM\OneToMany(targetEntity=Scene::class, mappedBy="eglise")
     */
    private $scenes;

    /**
     * @ORM\OneToMany(targetEntity=Pastorale::class, mappedBy="eglise")
     */
    private $pastorales;

    /**
     * @ORM\OneToMany(targetEntity=Presencepastorale::class, mappedBy="eglise")
     */
    private $presencepastorales;

    /**
     * @ORM\OneToMany(targetEntity=Nommination::class, mappedBy="eglise")
     */
    private $nomminations;

    /**
     * @ORM\OneToMany(targetEntity=Discipline::class, mappedBy="eglise")
     */
    private $disciplines;



    /**
     * @ORM\OneToMany(targetEntity=Conge::class, mappedBy="eglise")
     */
    private $conges;

    /**
     * @ORM\OneToMany(targetEntity=Evangelisation::class, mappedBy="eglise")
     */
    private $evangelisations;

    /**
     * @ORM\OneToMany(targetEntity=Ame::class, mappedBy="eglise")
     */
    private $ames;

    /**
     * @ORM\OneToMany(targetEntity=Invitecellule::class, mappedBy="eglise")
     */
    private $invitecellules;

    /**
     * @ORM\OneToMany(targetEntity=Invitefamille::class, mappedBy="eglise")
     */
    private $invitefamilles;

    /**
     * @ORM\OneToMany(targetEntity=Invitezone::class, mappedBy="eglise")
     */
    private $invitezones;

    /**
     * @ORM\OneToMany(targetEntity=Invitedepartement::class, mappedBy="eglise")
     */
    private $invitedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Invitegroupe::class, mappedBy="eglise")
     */
    private $invitegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Visite2::class, mappedBy="eglise")
     */
    private $visite2s;

    /**
     * @ORM\OneToMany(targetEntity=Depensecodim::class, mappedBy="eglise")
     */
    private $depensecodims;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $regionpastorale;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lastFideleNumber;

    /**
     * @ORM\OneToMany(targetEntity=Depensedepartement::class, mappedBy="eglise")
     */
    private $depensedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Depensegroupe::class, mappedBy="eglise")
     */
    private $depensegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Depensecellule::class, mappedBy="eglise")
     */
    private $depensecellules;

    /**
     * @ORM\OneToMany(targetEntity=Depensefamille::class, mappedBy="eglise")
     */
    private $depensefamilles;

    /**
     * @ORM\OneToMany(targetEntity=Depensezone::class, mappedBy="eglise")
     */
    private $depensezones;



  

   

   

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->quartiers = new ArrayCollection();
        $this->inscrires = new ArrayCollection();
        $this->dime = new ArrayCollection();
        $this->cellule = new ArrayCollection();
        $this->classecodim = new ArrayCollection();
        $this->cotisation = new ArrayCollection();
        $this->culte = new ArrayCollection();
        $this->cultecodim = new ArrayCollection();
        $this->departement = new ArrayCollection();
        $this->departementfidele = new ArrayCollection();
        $this->evenement = new ArrayCollection();
        $this->famille = new ArrayCollection();
        $this->groupe = new ArrayCollection();
        $this->groupefidele = new ArrayCollection();
        $this->offrande = new ArrayCollection();
        $this->operation = new ArrayCollection();
//        $this->parentenfant = new ArrayCollection();
        $this->patrimoine = new ArrayCollection();
        $this->projet = new ArrayCollection();
        $this->zone = new ArrayCollection();
        $this->fidelecotiser = new ArrayCollection();
        $this->sms = new ArrayCollection();
        $this->bapteme = new ArrayCollection();
        $this->mariage = new ArrayCollection();
        $this->fiancaille = new ArrayCollection();
        $this->naissance = new ArrayCollection();
        $this->recommandation = new ArrayCollection();
        $this->enfantactivites = new ArrayCollection();
        $this->couples = new ArrayCollection();
        $this->deces = new ArrayCollection();
        $this->cotiserdepartements = new ArrayCollection();
        $this->cotiserzones = new ArrayCollection();
        $this->seancezones = new ArrayCollection();
        $this->cotisationzones = new ArrayCollection();
        $this->zonecotisations = new ArrayCollection();
        $this->presencecellules = new ArrayCollection();
        $this->cotisationcellules = new ArrayCollection();
        $this->presencegroupes = new ArrayCollection();
        $this->presencedepartements = new ArrayCollection();
        $this->presencezones = new ArrayCollection();
        $this->presencefamilles = new ArrayCollection();
        $this->detailcotisations = new ArrayCollection();
        $this->detailcotisationzones = new ArrayCollection();
        $this->cotisationsociales = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->detailenfantactivites = new ArrayCollection();
        $this->cotisationparcellules = new ArrayCollection();
        $this->cotisationpardepartements = new ArrayCollection();
        $this->cotisationparfamilles = new ArrayCollection();
        $this->cotisationpargroupes = new ArrayCollection();
        $this->cotisationparzones = new ArrayCollection();
        $this->cotiserpardepartements = new ArrayCollection();
        $this->cotiserparcellules = new ArrayCollection();
        $this->cotiserparfamilles = new ArrayCollection();
        $this->cotiserpargroupes = new ArrayCollection();
        $this->detailparzones = new ArrayCollection();
        $this->detailpardepartements = new ArrayCollection();
        $this->detailpargroupes = new ArrayCollection();
        $this->detailparfamilles = new ArrayCollection();
        $this->detailparcellules = new ArrayCollection();
        $this->cotiserpazones = new ArrayCollection();
        $this->presenceculteecodims = new ArrayCollection();
        $this->visites = new ArrayCollection();
        $this->fideles = new ArrayCollection();
        $this->soldes = new ArrayCollection();
        $this->scenes = new ArrayCollection();
        $this->pastorales = new ArrayCollection();
        $this->presencepastorales = new ArrayCollection();
        $this->nomminations = new ArrayCollection();
        $this->disciplines = new ArrayCollection();
        $this->conges = new ArrayCollection();
        $this->evangelisations = new ArrayCollection();
        $this->ames = new ArrayCollection();
        $this->invitecellules = new ArrayCollection();
        $this->invitefamilles = new ArrayCollection();
        $this->invitezones = new ArrayCollection();
        $this->invitedepartements = new ArrayCollection();
        $this->invitegroupes = new ArrayCollection();
        $this->visite2s = new ArrayCollection();
        $this->depensecodims = new ArrayCollection();
        $this->depensedepartements = new ArrayCollection();
        $this->depensegroupes = new ArrayCollection();
        $this->depensecellules = new ArrayCollection();
        $this->depensefamilles = new ArrayCollection();
        $this->depensezones = new ArrayCollection();
   
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(?string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getContact1(): ?string
    {
        return $this->contact1;
    }

    public function setContact1(?string $contact1): self
    {
        $this->contact1 = $contact1;

        return $this;
    }

    public function getContact2(): ?string
    {
        return $this->contact2;
    }

    public function setContact2(?string $contact2): self
    {
        $this->contact2 = $contact2;

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->quartier;
    }

    public function setQuartier(?string $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getVerset(): ?string
    {
        return $this->verset;
    }

    public function setVerset(?string $verset): self
    {
        $this->verset = $verset;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): self
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getAgrement(): ?string
    {
        return $this->agrement;
    }

    public function setAgrement(?string $agrement): self
    {
        $this->agrement = $agrement;

        return $this;
    }



    public function getEtateglise(): ?bool
    {
        return $this->etateglise;
    }

    public function setEtateglise(?bool $etateglise): self
    {
        $this->etateglise = $etateglise;

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

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setEglise($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getEglise() === $this) {
                $user->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Quartier[]
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): self
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers[] = $quartier;
            $quartier->setEglise($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): self
    {
        if ($this->quartiers->removeElement($quartier)) {
            // set the owning side to null (unless already changed)
            if ($quartier->getEglise() === $this) {
                $quartier->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Inscrire[]
     */
    public function getInscrires(): Collection
    {
        return $this->inscrires;
    }

    public function addInscrire(Inscrire $inscrire): self
    {
        if (!$this->inscrires->contains($inscrire)) {
            $this->inscrires[] = $inscrire;
            $inscrire->setEglise($this);
        }

        return $this;
    }

    public function removeInscrire(Inscrire $inscrire): self
    {
        if ($this->inscrires->removeElement($inscrire)) {
            // set the owning side to null (unless already changed)
            if ($inscrire->getEglise() === $this) {
                $inscrire->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Dime[]
     */
    public function getDime(): Collection
    {
        return $this->dime;
    }

    public function addDime(Dime $dime): self
    {
        if (!$this->dime->contains($dime)) {
            $this->dime[] = $dime;
            $dime->setEglise($this);
        }

        return $this;
    }

    public function removeDime(Dime $dime): self
    {
        if ($this->dime->removeElement($dime)) {
            // set the owning side to null (unless already changed)
            if ($dime->getEglise() === $this) {
                $dime->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cellule[]
     */
    public function getCellule(): Collection
    {
        return $this->cellule;
    }

    public function addCellule(Cellule $cellule): self
    {
        if (!$this->cellule->contains($cellule)) {
            $this->cellule[] = $cellule;
            $cellule->setEglise($this);
        }

        return $this;
    }

    public function removeCellule(Cellule $cellule): self
    {
        if ($this->cellule->removeElement($cellule)) {
            // set the owning side to null (unless already changed)
            if ($cellule->getEglise() === $this) {
                $cellule->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classecodim[]
     */
    public function getClassecodim(): Collection
    {
        return $this->classecodim;
    }

    public function addClassecodim(Classecodim $classecodim): self
    {
        if (!$this->classecodim->contains($classecodim)) {
            $this->classecodim[] = $classecodim;
            $classecodim->setEglise($this);
        }

        return $this;
    }

    public function removeClassecodim(Classecodim $classecodim): self
    {
        if ($this->classecodim->removeElement($classecodim)) {
            // set the owning side to null (unless already changed)
            if ($classecodim->getEglise() === $this) {
                $classecodim->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cotisation[]
     */
    public function getCotisation(): Collection
    {
        return $this->cotisation;
    }

    public function addCotisation(Cotisation $cotisation): self
    {
        if (!$this->cotisation->contains($cotisation)) {
            $this->cotisation[] = $cotisation;
            $cotisation->setEglise($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): self
    {
        if ($this->cotisation->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getEglise() === $this) {
                $cotisation->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Culte[]
     */
    public function getCulte(): Collection
    {
        return $this->culte;
    }

    public function addCulte(Culte $culte): self
    {
        if (!$this->culte->contains($culte)) {
            $this->culte[] = $culte;
            $culte->setEglise($this);
        }

        return $this;
    }

    public function removeCulte(Culte $culte): self
    {
        if ($this->culte->removeElement($culte)) {
            // set the owning side to null (unless already changed)
            if ($culte->getEglise() === $this) {
                $culte->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cultecodim[]
     */
    public function getCultecodim(): Collection
    {
        return $this->cultecodim;
    }

    public function addCultecodim(Cultecodim $cultecodim): self
    {
        if (!$this->cultecodim->contains($cultecodim)) {
            $this->cultecodim[] = $cultecodim;
            $cultecodim->setEglise($this);
        }

        return $this;
    }

    public function removeCultecodim(Cultecodim $cultecodim): self
    {
        if ($this->cultecodim->removeElement($cultecodim)) {
            // set the owning side to null (unless already changed)
            if ($cultecodim->getEglise() === $this) {
                $cultecodim->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Departement[]
     */
    public function getDepartement(): Collection
    {
        return $this->departement;
    }

    public function addDepartement(Departement $departement): self
    {
        if (!$this->departement->contains($departement)) {
            $this->departement[] = $departement;
            $departement->setEglise($this);
        }

        return $this;
    }

    public function removeDepartement(Departement $departement): self
    {
        if ($this->departement->removeElement($departement)) {
            // set the owning side to null (unless already changed)
            if ($departement->getEglise() === $this) {
                $departement->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Departementfidele[]
     */
    public function getDepartementfidele(): Collection
    {
        return $this->departementfidele;
    }

    public function addDepartementfidele(Departementfidele $departementfidele): self
    {
        if (!$this->departementfidele->contains($departementfidele)) {
            $this->departementfidele[] = $departementfidele;
            $departementfidele->setEglise($this);
        }

        return $this;
    }

    public function removeDepartementfidele(Departementfidele $departementfidele): self
    {
        if ($this->departementfidele->removeElement($departementfidele)) {
            // set the owning side to null (unless already changed)
            if ($departementfidele->getEglise() === $this) {
                $departementfidele->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement[] = $evenement;
            $evenement->setEglise($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getEglise() === $this) {
                $evenement->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Famille[]
     */
    public function getFamille(): Collection
    {
        return $this->famille;
    }

    public function addFamille(Famille $famille): self
    {
        if (!$this->famille->contains($famille)) {
            $this->famille[] = $famille;
            $famille->setEglise($this);
        }

        return $this;
    }

    public function removeFamille(Famille $famille): self
    {
        if ($this->famille->removeElement($famille)) {
            // set the owning side to null (unless already changed)
            if ($famille->getEglise() === $this) {
                $famille->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupe(): Collection
    {
        return $this->groupe;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupe->contains($groupe)) {
            $this->groupe[] = $groupe;
            $groupe->setEglise($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupe->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getEglise() === $this) {
                $groupe->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Groupefidele[]
     */
    public function getGroupefidele(): Collection
    {
        return $this->groupefidele;
    }

    public function addGroupefidele(Groupefidele $groupefidele): self
    {
        if (!$this->groupefidele->contains($groupefidele)) {
            $this->groupefidele[] = $groupefidele;
            $groupefidele->setEglise($this);
        }

        return $this;
    }

    public function removeGroupefidele(Groupefidele $groupefidele): self
    {
        if ($this->groupefidele->removeElement($groupefidele)) {
            // set the owning side to null (unless already changed)
            if ($groupefidele->getEglise() === $this) {
                $groupefidele->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Offrande[]
     */
    public function getOffrande(): Collection
    {
        return $this->offrande;
    }

    public function addOffrande(Offrande $offrande): self
    {
        if (!$this->offrande->contains($offrande)) {
            $this->offrande[] = $offrande;
            $offrande->setEglise($this);
        }

        return $this;
    }

    public function removeOffrande(Offrande $offrande): self
    {
        if ($this->offrande->removeElement($offrande)) {
            // set the owning side to null (unless already changed)
            if ($offrande->getEglise() === $this) {
                $offrande->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getOperation(): Collection
    {
        return $this->operation;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operation->contains($operation)) {
            $this->operation[] = $operation;
            $operation->setEglise($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operation->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getEglise() === $this) {
                $operation->setEglise(null);
            }
        }

        return $this;
    }

//    /**
//     * @return Collection|Parentenfant[]
//     */
//    public function getParentenfant(): Collection
//    {
//        return $this->parentenfant;
//    }
//
//    public function addParentenfant(Parentenfant $parentenfant): self
//    {
//        if (!$this->parentenfant->contains($parentenfant)) {
//            $this->parentenfant[] = $parentenfant;
//            $parentenfant->setEglise($this);
//        }
//
//        return $this;
//    }
//
//    public function removeParentenfant(Parentenfant $parentenfant): self
//    {
//        if ($this->parentenfant->removeElement($parentenfant)) {
//            // set the owning side to null (unless already changed)
//            if ($parentenfant->getEglise() === $this) {
//                $parentenfant->setEglise(null);
//            }
//        }
//
//        return $this;
//    }

    /**
     * @return Collection|Patrimoine[]
     */
    public function getPatrimoine(): Collection
    {
        return $this->patrimoine;
    }

    public function addPatrimoine(Patrimoine $patrimoine): self
    {
        if (!$this->patrimoine->contains($patrimoine)) {
            $this->patrimoine[] = $patrimoine;
            $patrimoine->setEglise($this);
        }

        return $this;
    }

    public function removePatrimoine(Patrimoine $patrimoine): self
    {
        if ($this->patrimoine->removeElement($patrimoine)) {
            // set the owning side to null (unless already changed)
            if ($patrimoine->getEglise() === $this) {
                $patrimoine->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Projet[]
     */
    public function getProjet(): Collection
    {
        return $this->projet;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->projet->contains($projet)) {
            $this->projet[] = $projet;
            $projet->setEglise($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projet->removeElement($projet)) {
            // set the owning side to null (unless already changed)
            if ($projet->getEglise() === $this) {
                $projet->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Zone[]
     */
    public function getZone(): Collection
    {
        return $this->zone;
    }

    public function addZone(Zone $zone): self
    {
        if (!$this->zone->contains($zone)) {
            $this->zone[] = $zone;
            $zone->setEglise($this);
        }

        return $this;
    }

    public function removeZone(Zone $zone): self
    {
        if ($this->zone->removeElement($zone)) {
            // set the owning side to null (unless already changed)
            if ($zone->getEglise() === $this) {
                $zone->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fidelecotiser[]
     */
    public function getFidelecotiser(): Collection
    {
        return $this->fidelecotiser;
    }

    public function addFidelecotiser(Fidelecotiser $fidelecotiser): self
    {
        if (!$this->fidelecotiser->contains($fidelecotiser)) {
            $this->fidelecotiser[] = $fidelecotiser;
            $fidelecotiser->setEglise($this);
        }

        return $this;
    }

    public function removeFidelecotiser(Fidelecotiser $fidelecotiser): self
    {
        if ($this->fidelecotiser->removeElement($fidelecotiser)) {
            // set the owning side to null (unless already changed)
            if ($fidelecotiser->getEglise() === $this) {
                $fidelecotiser->setEglise(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function removeFidele(Fidele $fidele): self
    {
        if ($this->fidele->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getEglise() === $this) {
                $fidele->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sms[]
     */
    public function getSms(): Collection
    {
        return $this->sms;
    }

    public function addSms(Sms $sms): self
    {
        if (!$this->sms->contains($sms)) {
            $this->sms[] = $sms;
            $sms->setEglise($this);
        }

        return $this;
    }

    public function removeSms(Sms $sms): self
    {
        if ($this->sms->removeElement($sms)) {
            // set the owning side to null (unless already changed)
            if ($sms->getEglise() === $this) {
                $sms->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bapteme[]
     */
    public function getBapteme(): Collection
    {
        return $this->bapteme;
    }

    public function addBapteme(Bapteme $bapteme): self
    {
        if (!$this->bapteme->contains($bapteme)) {
            $this->bapteme[] = $bapteme;
            $bapteme->setEglise($this);
        }

        return $this;
    }

    public function removeBapteme(Bapteme $bapteme): self
    {
        if ($this->bapteme->removeElement($bapteme)) {
            // set the owning side to null (unless already changed)
            if ($bapteme->getEglise() === $this) {
                $bapteme->setEglise(null);
            }
        }

        return $this;
    }

 

    /**
     * @return Collection|Mariage[]
     */
    public function getMariage(): Collection
    {
        return $this->mariage;
    }

    public function addMariage(Mariage $mariage): self
    {
        if (!$this->mariage->contains($mariage)) {
            $this->mariage[] = $mariage;
            $mariage->setEglise($this);
        }

        return $this;
    }

    public function removeMariage(Mariage $mariage): self
    {
        if ($this->mariage->removeElement($mariage)) {
            // set the owning side to null (unless already changed)
            if ($mariage->getEglise() === $this) {
                $mariage->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fiancaille[]
     */
    public function getFiancaille(): Collection
    {
        return $this->fiancaille;
    }

    public function addFiancaille(Fiancaille $fiancaille): self
    {
        if (!$this->fiancaille->contains($fiancaille)) {
            $this->fiancaille[] = $fiancaille;
            $fiancaille->setEglise($this);
        }

        return $this;
    }

    public function removeFiancaille(Fiancaille $fiancaille): self
    {
        if ($this->fiancaille->removeElement($fiancaille)) {
            // set the owning side to null (unless already changed)
            if ($fiancaille->getEglise() === $this) {
                $fiancaille->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Naissance[]
     */
    public function getNaissance(): Collection
    {
        return $this->naissance;
    }

    public function addNaissance(Naissance $naissance): self
    {
        if (!$this->naissance->contains($naissance)) {
            $this->naissance[] = $naissance;
            $naissance->setEglise($this);
        }

        return $this;
    }

    public function removeNaissance(Naissance $naissance): self
    {
        if ($this->naissance->removeElement($naissance)) {
            // set the owning side to null (unless already changed)
            if ($naissance->getEglise() === $this) {
                $naissance->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Recommandation[]
     */
    public function getRecommandation(): Collection
    {
        return $this->recommandation;
    }

    public function addRecommandation(Recommandation $recommandation): self
    {
        if (!$this->recommandation->contains($recommandation)) {
            $this->recommandation[] = $recommandation;
            $recommandation->setEglise($this);
        }

        return $this;
    }

    public function removeRecommandation(Recommandation $recommandation): self
    {
        if ($this->recommandation->removeElement($recommandation)) {
            // set the owning side to null (unless already changed)
            if ($recommandation->getEglise() === $this) {
                $recommandation->setEglise(null);
            }
        }

        return $this;
    }

    public function getArrete(): ?string
    {
        return $this->arrete;
    }

    public function setArrete(?string $arrete): self
    {
        $this->arrete = $arrete;

        return $this;
    }

    public function getCommunaute(): ?Communaute
    {
        return $this->communaute;
    }

    public function setCommunaute(?Communaute $communaute): self
    {
        $this->communaute = $communaute;

        return $this;
    }

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCongregation(): ?string
    {
        return $this->congregation;
    }

    public function setCongregation(?string $congregation): self
    {
        $this->congregation = $congregation;

        return $this;
    }

    public function getAdministrateur(): ?string
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?string $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function __toString() {
        return $this->getDenomination();
    }



    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * @return Collection<int, Enfantactivite>
     */
    public function getEnfantactivites(): Collection
    {
        return $this->enfantactivites;
    }

    public function addEnfantactivite(Enfantactivite $enfantactivite): self
    {
        if (!$this->enfantactivites->contains($enfantactivite)) {
            $this->enfantactivites[] = $enfantactivite;
            $enfantactivite->setEglise($this);
        }

        return $this;
    }

    public function removeEnfantactivite(Enfantactivite $enfantactivite): self
    {
        if ($this->enfantactivites->removeElement($enfantactivite)) {
            // set the owning side to null (unless already changed)
            if ($enfantactivite->getEglise() === $this) {
                $enfantactivite->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Couple>
     */
    public function getCouples(): Collection
    {
        return $this->couples;
    }

    public function addCouple(Couple $couple): self
    {
        if (!$this->couples->contains($couple)) {
            $this->couples[] = $couple;
            $couple->setEglise($this);
        }

        return $this;
    }

    public function removeCouple(Couple $couple): self
    {
        if ($this->couples->removeElement($couple)) {
            // set the owning side to null (unless already changed)
            if ($couple->getEglise() === $this) {
                $couple->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Deces>
     */
    public function getDeces(): Collection
    {
        return $this->deces;
    }

    public function addDece(Deces $dece): self
    {
        if (!$this->deces->contains($dece)) {
            $this->deces[] = $dece;
            $dece->setEglise($this);
        }

        return $this;
    }

    public function removeDece(Deces $dece): self
    {
        if ($this->deces->removeElement($dece)) {
            // set the owning side to null (unless already changed)
            if ($dece->getEglise() === $this) {
                $dece->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotiserdepartement>
     */
    public function getCotiserdepartements(): Collection
    {
        return $this->cotiserdepartements;
    }

    public function addCotiserdepartement(Cotiserdepartement $cotiserdepartement): self
    {
        if (!$this->cotiserdepartements->contains($cotiserdepartement)) {
            $this->cotiserdepartements[] = $cotiserdepartement;
            $cotiserdepartement->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserdepartement(Cotiserdepartement $cotiserdepartement): self
    {
        if ($this->cotiserdepartements->removeElement($cotiserdepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotiserdepartement->getEglise() === $this) {
                $cotiserdepartement->setEglise(null);
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
            $cotiserzone->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserzone(Cotiserzone $cotiserzone): self
    {
        if ($this->cotiserzones->removeElement($cotiserzone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserzone->getEglise() === $this) {
                $cotiserzone->setEglise(null);
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
            $seancezone->setEglise($this);
        }

        return $this;
    }

    public function removeSeancezone(Seancezone $seancezone): self
    {
        if ($this->seancezones->removeElement($seancezone)) {
            // set the owning side to null (unless already changed)
            if ($seancezone->getEglise() === $this) {
                $seancezone->setEglise(null);
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
            $cotisationzone->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationzone(Cotisationzone $cotisationzone): self
    {
        if ($this->cotisationzones->removeElement($cotisationzone)) {
            // set the owning side to null (unless already changed)
            if ($cotisationzone->getEglise() === $this) {
                $cotisationzone->setEglise(null);
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
            $zonecotisation->setEglise($this);
        }

        return $this;
    }

    public function removeZonecotisation(Zonecotisation $zonecotisation): self
    {
        if ($this->zonecotisations->removeElement($zonecotisation)) {
            // set the owning side to null (unless already changed)
            if ($zonecotisation->getEglise() === $this) {
                $zonecotisation->setEglise(null);
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
            $presencecellule->setEglise($this);
        }

        return $this;
    }

    public function removePresencecellule(Presencecellule $presencecellule): self
    {
        if ($this->presencecellules->removeElement($presencecellule)) {
            // set the owning side to null (unless already changed)
            if ($presencecellule->getEglise() === $this) {
                $presencecellule->setEglise(null);
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
            $cotisationcellule->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationcellule(Cotisationcellule $cotisationcellule): self
    {
        if ($this->cotisationcellules->removeElement($cotisationcellule)) {
            // set the owning side to null (unless already changed)
            if ($cotisationcellule->getEglise() === $this) {
                $cotisationcellule->setEglise(null);
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
            $presencegroupe->setEglise($this);
        }

        return $this;
    }

    public function removePresencegroupe(Presencegroupe $presencegroupe): self
    {
        if ($this->presencegroupes->removeElement($presencegroupe)) {
            // set the owning side to null (unless already changed)
            if ($presencegroupe->getEglise() === $this) {
                $presencegroupe->setEglise(null);
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
            $presencedepartement->setEglise($this);
        }

        return $this;
    }

    public function removePresencedepartement(Presencedepartement $presencedepartement): self
    {
        if ($this->presencedepartements->removeElement($presencedepartement)) {
            // set the owning side to null (unless already changed)
            if ($presencedepartement->getEglise() === $this) {
                $presencedepartement->setEglise(null);
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
            $presencezone->setEglise($this);
        }

        return $this;
    }

    public function removePresencezone(Presencezone $presencezone): self
    {
        if ($this->presencezones->removeElement($presencezone)) {
            // set the owning side to null (unless already changed)
            if ($presencezone->getEglise() === $this) {
                $presencezone->setEglise(null);
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
            $presencefamille->setEglise($this);
        }

        return $this;
    }

    public function removePresencefamille(Presencefamille $presencefamille): self
    {
        if ($this->presencefamilles->removeElement($presencefamille)) {
            // set the owning side to null (unless already changed)
            if ($presencefamille->getEglise() === $this) {
                $presencefamille->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisation>
     */
    public function getDetailcotisations(): Collection
    {
        return $this->detailcotisations;
    }

    public function addDetailcotisation(Detailcotisation $detailcotisation): self
    {
        if (!$this->detailcotisations->contains($detailcotisation)) {
            $this->detailcotisations[] = $detailcotisation;
            $detailcotisation->setEglise($this);
        }

        return $this;
    }

    public function removeDetailcotisation(Detailcotisation $detailcotisation): self
    {
        if ($this->detailcotisations->removeElement($detailcotisation)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisation->getEglise() === $this) {
                $detailcotisation->setEglise(null);
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
            $detailcotisationzone->setEglise($this);
        }

        return $this;
    }

    public function removeDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self
    {
        if ($this->detailcotisationzones->removeElement($detailcotisationzone)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationzone->getEglise() === $this) {
                $detailcotisationzone->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationsociale>
     */
    public function getCotisationsociales(): Collection
    {
        return $this->cotisationsociales;
    }

    public function addCotisationsociale(Cotisationsociale $cotisationsociale): self
    {
        if (!$this->cotisationsociales->contains($cotisationsociale)) {
            $this->cotisationsociales[] = $cotisationsociale;
            $cotisationsociale->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationsociale(Cotisationsociale $cotisationsociale): self
    {
        if ($this->cotisationsociales->removeElement($cotisationsociale)) {
            // set the owning side to null (unless already changed)
            if ($cotisationsociale->getEglise() === $this) {
                $cotisationsociale->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setEglise($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getEglise() === $this) {
                $paiement->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailenfantactivite>
     */
    public function getDetailenfantactivites(): Collection
    {
        return $this->detailenfantactivites;
    }

    public function addDetailenfantactivite(Detailenfantactivite $detailenfantactivite): self
    {
        if (!$this->detailenfantactivites->contains($detailenfantactivite)) {
            $this->detailenfantactivites[] = $detailenfantactivite;
            $detailenfantactivite->setEglise($this);
        }

        return $this;
    }

    public function removeDetailenfantactivite(Detailenfantactivite $detailenfantactivite): self
    {
        if ($this->detailenfantactivites->removeElement($detailenfantactivite)) {
            // set the owning side to null (unless already changed)
            if ($detailenfantactivite->getEglise() === $this) {
                $detailenfantactivite->setEglise(null);
            }
        }

        return $this;
    }

    public function getDeleted2At(): ?\DateTimeInterface
    {
        return $this->deleted2At;
    }

    public function setDeleted2At(?\DateTimeInterface $deleted2At): self
    {
        $this->deleted2At = $deleted2At;

        return $this;
    }

    /**
     * @return Collection<int, Cotisationparcellule>
     */
    public function getCotisationparcellules(): Collection
    {
        return $this->cotisationparcellules;
    }

    public function addCotisationparcellule(Cotisationparcellule $cotisationparcellule): self
    {
        if (!$this->cotisationparcellules->contains($cotisationparcellule)) {
            $this->cotisationparcellules[] = $cotisationparcellule;
            $cotisationparcellule->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationparcellule(Cotisationparcellule $cotisationparcellule): self
    {
        if ($this->cotisationparcellules->removeElement($cotisationparcellule)) {
            // set the owning side to null (unless already changed)
            if ($cotisationparcellule->getEglise() === $this) {
                $cotisationparcellule->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationpardepartement>
     */
    public function getCotisationpardepartements(): Collection
    {
        return $this->cotisationpardepartements;
    }

    public function addCotisationpardepartement(Cotisationpardepartement $cotisationpardepartement): self
    {
        if (!$this->cotisationpardepartements->contains($cotisationpardepartement)) {
            $this->cotisationpardepartements[] = $cotisationpardepartement;
            $cotisationpardepartement->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationpardepartement(Cotisationpardepartement $cotisationpardepartement): self
    {
        if ($this->cotisationpardepartements->removeElement($cotisationpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotisationpardepartement->getEglise() === $this) {
                $cotisationpardepartement->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationparfamille>
     */
    public function getCotisationparfamilles(): Collection
    {
        return $this->cotisationparfamilles;
    }

    public function addCotisationparfamille(Cotisationparfamille $cotisationparfamille): self
    {
        if (!$this->cotisationparfamilles->contains($cotisationparfamille)) {
            $this->cotisationparfamilles[] = $cotisationparfamille;
            $cotisationparfamille->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationparfamille(Cotisationparfamille $cotisationparfamille): self
    {
        if ($this->cotisationparfamilles->removeElement($cotisationparfamille)) {
            // set the owning side to null (unless already changed)
            if ($cotisationparfamille->getEglise() === $this) {
                $cotisationparfamille->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationpargroupe>
     */
    public function getCotisationpargroupes(): Collection
    {
        return $this->cotisationpargroupes;
    }

    public function addCotisationpargroupe(Cotisationpargroupe $cotisationpargroupe): self
    {
        if (!$this->cotisationpargroupes->contains($cotisationpargroupe)) {
            $this->cotisationpargroupes[] = $cotisationpargroupe;
            $cotisationpargroupe->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationpargroupe(Cotisationpargroupe $cotisationpargroupe): self
    {
        if ($this->cotisationpargroupes->removeElement($cotisationpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($cotisationpargroupe->getEglise() === $this) {
                $cotisationpargroupe->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cotisationparzone>
     */
    public function getCotisationparzones(): Collection
    {
        return $this->cotisationparzones;
    }

    public function addCotisationparzone(Cotisationparzone $cotisationparzone): self
    {
        if (!$this->cotisationparzones->contains($cotisationparzone)) {
            $this->cotisationparzones[] = $cotisationparzone;
            $cotisationparzone->setEglise($this);
        }

        return $this;
    }

    public function removeCotisationparzone(Cotisationparzone $cotisationparzone): self
    {
        if ($this->cotisationparzones->removeElement($cotisationparzone)) {
            // set the owning side to null (unless already changed)
            if ($cotisationparzone->getEglise() === $this) {
                $cotisationparzone->setEglise(null);
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
            $cotiserpardepartement->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserpardepartement(Cotiserpardepartement $cotiserpardepartement): self
    {
        if ($this->cotiserpardepartements->removeElement($cotiserpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpardepartement->getEglise() === $this) {
                $cotiserpardepartement->setEglise(null);
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
            $cotiserparcellule->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserparcellule(Cotiserparcellule $cotiserparcellule): self
    {
        if ($this->cotiserparcellules->removeElement($cotiserparcellule)) {
            // set the owning side to null (unless already changed)
            if ($cotiserparcellule->getEglise() === $this) {
                $cotiserparcellule->setEglise(null);
            }
        }

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
            $cotiserparfamille->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserparfamille(Cotiserparfamille $cotiserparfamille): self
    {
        if ($this->cotiserparfamilles->removeElement($cotiserparfamille)) {
            // set the owning side to null (unless already changed)
            if ($cotiserparfamille->getEglise() === $this) {
                $cotiserparfamille->setEglise(null);
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
            $cotiserpargroupe->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserpargroupe(Cotiserpargroupe $cotiserpargroupe): self
    {
        if ($this->cotiserpargroupes->removeElement($cotiserpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpargroupe->getEglise() === $this) {
                $cotiserpargroupe->setEglise(null);
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
            $detailparzone->setEglise($this);
        }

        return $this;
    }

    public function removeDetailparzone(Detailparzone $detailparzone): self
    {
        if ($this->detailparzones->removeElement($detailparzone)) {
            // set the owning side to null (unless already changed)
            if ($detailparzone->getEglise() === $this) {
                $detailparzone->setEglise(null);
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
            $detailpardepartement->setEglise($this);
        }

        return $this;
    }

    public function removeDetailpardepartement(Detailpardepartement $detailpardepartement): self
    {
        if ($this->detailpardepartements->removeElement($detailpardepartement)) {
            // set the owning side to null (unless already changed)
            if ($detailpardepartement->getEglise() === $this) {
                $detailpardepartement->setEglise(null);
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
            $detailpargroupe->setEglise($this);
        }

        return $this;
    }

    public function removeDetailpargroupe(Detailpargroupe $detailpargroupe): self
    {
        if ($this->detailpargroupes->removeElement($detailpargroupe)) {
            // set the owning side to null (unless already changed)
            if ($detailpargroupe->getEglise() === $this) {
                $detailpargroupe->setEglise(null);
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
            $detailparfamille->setEglise($this);
        }

        return $this;
    }

    public function removeDetailparfamille(Detailparfamille $detailparfamille): self
    {
        if ($this->detailparfamilles->removeElement($detailparfamille)) {
            // set the owning side to null (unless already changed)
            if ($detailparfamille->getEglise() === $this) {
                $detailparfamille->setEglise(null);
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
            $detailparcellule->setEglise($this);
        }

        return $this;
    }

    public function removeDetailparcellule(Detailparcellule $detailparcellule): self
    {
        if ($this->detailparcellules->removeElement($detailparcellule)) {
            // set the owning side to null (unless already changed)
            if ($detailparcellule->getEglise() === $this) {
                $detailparcellule->setEglise(null);
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
            $cotiserpazone->setEglise($this);
        }

        return $this;
    }

    public function removeCotiserpazone(Cotiserpazone $cotiserpazone): self
    {
        if ($this->cotiserpazones->removeElement($cotiserpazone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserpazone->getEglise() === $this) {
                $cotiserpazone->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presenceculteecodim>
     */
    public function getPresenceculteecodims(): Collection
    {
        return $this->presenceculteecodims;
    }

    public function addPresenceculteecodim(Presenceculteecodim $presenceculteecodim): self
    {
        if (!$this->presenceculteecodims->contains($presenceculteecodim)) {
            $this->presenceculteecodims[] = $presenceculteecodim;
            $presenceculteecodim->setEglise($this);
        }

        return $this;
    }

    public function removePresenceculteecodim(Presenceculteecodim $presenceculteecodim): self
    {
        if ($this->presenceculteecodims->removeElement($presenceculteecodim)) {
            // set the owning side to null (unless already changed)
            if ($presenceculteecodim->getEglise() === $this) {
                $presenceculteecodim->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Visite>
     */
    public function getVisites(): Collection
    {
        return $this->visites;
    }

    public function addVisite(Visite $visite): self
    {
        if (!$this->visites->contains($visite)) {
            $this->visites[] = $visite;
            $visite->setEglise($this);
        }

        return $this;
    }

    public function removeVisite(Visite $visite): self
    {
        if ($this->visites->removeElement($visite)) {
            // set the owning side to null (unless already changed)
            if ($visite->getEglise() === $this) {
                $visite->setEglise(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Fidele>
     */
    public function getFideles(): Collection
    {
        return $this->fideles;
    }

    public function addFidele(Fidele $fidele): self
    {
        if (!$this->fideles->contains($fidele)) {
            $this->fideles[] = $fidele;
            $fidele->setEglise($this);
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

   
    /**
     * @return Collection<int, Solde>
     */
    public function getSoldes(): Collection
    {
        return $this->soldes;
    }

    public function addSolde(Solde $solde): self
    {
        if (!$this->soldes->contains($solde)) {
            $this->soldes[] = $solde;
            $solde->setEglise($this);
        }

        return $this;
    }

    public function removeSolde(Solde $solde): self
    {
        if ($this->soldes->removeElement($solde)) {
            // set the owning side to null (unless already changed)
            if ($solde->getEglise() === $this) {
                $solde->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Scene>
     */
    public function getScenes(): Collection
    {
        return $this->scenes;
    }

    public function addScene(Scene $scene): self
    {
        if (!$this->scenes->contains($scene)) {
            $this->scenes[] = $scene;
            $scene->setEglise($this);
        }

        return $this;
    }

    public function removeScene(Scene $scene): self
    {
        if ($this->scenes->removeElement($scene)) {
            // set the owning side to null (unless already changed)
            if ($scene->getEglise() === $this) {
                $scene->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pastorale>
     */
    public function getPastorales(): Collection
    {
        return $this->pastorales;
    }

    public function addPastorale(Pastorale $pastorale): self
    {
        if (!$this->pastorales->contains($pastorale)) {
            $this->pastorales[] = $pastorale;
            $pastorale->setEglise($this);
        }

        return $this;
    }

    public function removePastorale(Pastorale $pastorale): self
    {
        if ($this->pastorales->removeElement($pastorale)) {
            // set the owning side to null (unless already changed)
            if ($pastorale->getEglise() === $this) {
                $pastorale->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencepastorale>
     */
    public function getPresencepastorales(): Collection
    {
        return $this->presencepastorales;
    }

    public function addPresencepastorale(Presencepastorale $presencepastorale): self
    {
        if (!$this->presencepastorales->contains($presencepastorale)) {
            $this->presencepastorales[] = $presencepastorale;
            $presencepastorale->setEglise($this);
        }

        return $this;
    }

    public function removePresencepastorale(Presencepastorale $presencepastorale): self
    {
        if ($this->presencepastorales->removeElement($presencepastorale)) {
            // set the owning side to null (unless already changed)
            if ($presencepastorale->getEglise() === $this) {
                $presencepastorale->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Nommination>
     */
    public function getNomminations(): Collection
    {
        return $this->nomminations;
    }

    public function addNommination(Nommination $nommination): self
    {
        if (!$this->nomminations->contains($nommination)) {
            $this->nomminations[] = $nommination;
            $nommination->setEglise($this);
        }

        return $this;
    }

    public function removeNommination(Nommination $nommination): self
    {
        if ($this->nomminations->removeElement($nommination)) {
            // set the owning side to null (unless already changed)
            if ($nommination->getEglise() === $this) {
                $nommination->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discipline>
     */
    public function getDisciplines(): Collection
    {
        return $this->disciplines;
    }

    public function addDiscipline(Discipline $discipline): self
    {
        if (!$this->disciplines->contains($discipline)) {
            $this->disciplines[] = $discipline;
            $discipline->setEglise($this);
        }

        return $this;
    }

    public function removeDiscipline(Discipline $discipline): self
    {
        if ($this->disciplines->removeElement($discipline)) {
            // set the owning side to null (unless already changed)
            if ($discipline->getEglise() === $this) {
                $discipline->setEglise(null);
            }
        }

        return $this;
    }

   
    /**
     * @return Collection<int, Conge>
     */
    public function getConges(): Collection
    {
        return $this->conges;
    }

    public function addConge(Conge $conge): self
    {
        if (!$this->conges->contains($conge)) {
            $this->conges[] = $conge;
            $conge->setEglise($this);
        }

        return $this;
    }

    public function removeConge(Conge $conge): self
    {
        if ($this->conges->removeElement($conge)) {
            // set the owning side to null (unless already changed)
            if ($conge->getEglise() === $this) {
                $conge->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evangelisation>
     */
    public function getEvangelisations(): Collection
    {
        return $this->evangelisations;
    }

    public function addEvangelisation(Evangelisation $evangelisation): self
    {
        if (!$this->evangelisations->contains($evangelisation)) {
            $this->evangelisations[] = $evangelisation;
            $evangelisation->setEglise($this);
        }

        return $this;
    }

    public function removeEvangelisation(Evangelisation $evangelisation): self
    {
        if ($this->evangelisations->removeElement($evangelisation)) {
            // set the owning side to null (unless already changed)
            if ($evangelisation->getEglise() === $this) {
                $evangelisation->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ame>
     */
    public function getAmes(): Collection
    {
        return $this->ames;
    }

    public function addAme(Ame $ame): self
    {
        if (!$this->ames->contains($ame)) {
            $this->ames[] = $ame;
            $ame->setEglise($this);
        }

        return $this;
    }

    public function removeAme(Ame $ame): self
    {
        if ($this->ames->removeElement($ame)) {
            // set the owning side to null (unless already changed)
            if ($ame->getEglise() === $this) {
                $ame->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitecellule>
     */
    public function getInvitecellules(): Collection
    {
        return $this->invitecellules;
    }

    public function addInvitecellule(Invitecellule $invitecellule): self
    {
        if (!$this->invitecellules->contains($invitecellule)) {
            $this->invitecellules[] = $invitecellule;
            $invitecellule->setEglise($this);
        }

        return $this;
    }

    public function removeInvitecellule(Invitecellule $invitecellule): self
    {
        if ($this->invitecellules->removeElement($invitecellule)) {
            // set the owning side to null (unless already changed)
            if ($invitecellule->getEglise() === $this) {
                $invitecellule->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitefamille>
     */
    public function getInvitefamilles(): Collection
    {
        return $this->invitefamilles;
    }

    public function addInvitefamille(Invitefamille $invitefamille): self
    {
        if (!$this->invitefamilles->contains($invitefamille)) {
            $this->invitefamilles[] = $invitefamille;
            $invitefamille->setEglise($this);
        }

        return $this;
    }

    public function removeInvitefamille(Invitefamille $invitefamille): self
    {
        if ($this->invitefamilles->removeElement($invitefamille)) {
            // set the owning side to null (unless already changed)
            if ($invitefamille->getEglise() === $this) {
                $invitefamille->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitezone>
     */
    public function getInvitezones(): Collection
    {
        return $this->invitezones;
    }

    public function addInvitezone(Invitezone $invitezone): self
    {
        if (!$this->invitezones->contains($invitezone)) {
            $this->invitezones[] = $invitezone;
            $invitezone->setEglise($this);
        }

        return $this;
    }

    public function removeInvitezone(Invitezone $invitezone): self
    {
        if ($this->invitezones->removeElement($invitezone)) {
            // set the owning side to null (unless already changed)
            if ($invitezone->getEglise() === $this) {
                $invitezone->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitedepartement>
     */
    public function getInvitedepartements(): Collection
    {
        return $this->invitedepartements;
    }

    public function addInvitedepartement(Invitedepartement $invitedepartement): self
    {
        if (!$this->invitedepartements->contains($invitedepartement)) {
            $this->invitedepartements[] = $invitedepartement;
            $invitedepartement->setEglise($this);
        }

        return $this;
    }

    public function removeInvitedepartement(Invitedepartement $invitedepartement): self
    {
        if ($this->invitedepartements->removeElement($invitedepartement)) {
            // set the owning side to null (unless already changed)
            if ($invitedepartement->getEglise() === $this) {
                $invitedepartement->setEglise(null);
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
            $invitegroupe->setEglise($this);
        }

        return $this;
    }

    public function removeInvitegroupe(Invitegroupe $invitegroupe): self
    {
        if ($this->invitegroupes->removeElement($invitegroupe)) {
            // set the owning side to null (unless already changed)
            if ($invitegroupe->getEglise() === $this) {
                $invitegroupe->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Visite2>
     */
    public function getVisite2s(): Collection
    {
        return $this->visite2s;
    }

    public function addVisite2(Visite2 $visite2): self
    {
        if (!$this->visite2s->contains($visite2)) {
            $this->visite2s[] = $visite2;
            $visite2->setEglise($this);
        }

        return $this;
    }

    public function removeVisite2(Visite2 $visite2): self
    {
        if ($this->visite2s->removeElement($visite2)) {
            // set the owning side to null (unless already changed)
            if ($visite2->getEglise() === $this) {
                $visite2->setEglise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depensecodim>
     */
    public function getDepensecodims(): Collection
    {
        return $this->depensecodims;
    }

    public function addDepensecodim(Depensecodim $depensecodim): self
    {
        if (!$this->depensecodims->contains($depensecodim)) {
            $this->depensecodims[] = $depensecodim;
            $depensecodim->setEglise($this);
        }

        return $this;
    }

    public function removeDepensecodim(Depensecodim $depensecodim): self
    {
        if ($this->depensecodims->removeElement($depensecodim)) {
            // set the owning side to null (unless already changed)
            if ($depensecodim->getEglise() === $this) {
                $depensecodim->setEglise(null);
            }
        }

        return $this;
    }

    public function getRegionpastorale(): ?string
    {
        return $this->regionpastorale;
    }

    public function setRegionpastorale(?string $regionpastorale): self
    {
        $this->regionpastorale = $regionpastorale;

        return $this;
    }

    public function getLastFideleNumber(): ?int
    {
        return $this->lastFideleNumber;
    }

    public function setLastFideleNumber(?int $lastFideleNumber): self
    {
        $this->lastFideleNumber = $lastFideleNumber;

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
            $depensedepartement->setEglise($this);
        }

        return $this;
    }

    public function removeDepensedepartement(Depensedepartement $depensedepartement): self
    {
        if ($this->depensedepartements->removeElement($depensedepartement)) {
            // set the owning side to null (unless already changed)
            if ($depensedepartement->getEglise() === $this) {
                $depensedepartement->setEglise(null);
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
            $depensegroupe->setEglise($this);
        }

        return $this;
    }

    public function removeDepensegroupe(Depensegroupe $depensegroupe): self
    {
        if ($this->depensegroupes->removeElement($depensegroupe)) {
            // set the owning side to null (unless already changed)
            if ($depensegroupe->getEglise() === $this) {
                $depensegroupe->setEglise(null);
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
            $depensecellule->setEglise($this);
        }

        return $this;
    }

    public function removeDepensecellule(Depensecellule $depensecellule): self
    {
        if ($this->depensecellules->removeElement($depensecellule)) {
            // set the owning side to null (unless already changed)
            if ($depensecellule->getEglise() === $this) {
                $depensecellule->setEglise(null);
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
            $depensefamille->setEglise($this);
        }

        return $this;
    }

    public function removeDepensefamille(Depensefamille $depensefamille): self
    {
        if ($this->depensefamilles->removeElement($depensefamille)) {
            // set the owning side to null (unless already changed)
            if ($depensefamille->getEglise() === $this) {
                $depensefamille->setEglise(null);
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
            $depensezone->setEglise($this);
        }

        return $this;
    }

    public function removeDepensezone(Depensezone $depensezone): self
    {
        if ($this->depensezones->removeElement($depensezone)) {
            // set the owning side to null (unless already changed)
            if ($depensezone->getEglise() === $this) {
                $depensezone->setEglise(null);
            }
        }

        return $this;
    }

  
}
