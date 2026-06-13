<?php

namespace App\Entity;

use App\Repository\FideleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FideleRepository::class)
 * 
 */
class Fidele extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     * @Groups("public")
     */
    private $typefidele;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("public")
     */
    private $nomfidele;

    /**
     *  @Groups("public")
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $contact1;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $contactwhatssap;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("public")
     */
    private $nbrenfant;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     * @Groups("public")
     */
    private $statutmatri;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("public")
     */
    private $datenaiss;

       /**
     * @ORM\OneToMany(targetEntity=Scene::class, mappedBy="pasteur1")
     */
    private $scenes;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $numpiece;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $typepiece;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $comptefacebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $domaineactivite;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $profession;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $groupesang;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("public")
     */
    private $dateconversion;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     * @Groups("public")
     */
    private $stutbapteme;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("public")
     */
    private $datebapteme;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $lieubapteme;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $pasteurbapteme;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $anciennecommunaute;

    /**
     * @ORM\Column(type="string", length=128, nullable=true, unique=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="fideles")
     */
    private $cellule;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatfidele;

    /**
     * @ORM\OneToMany(targetEntity=Groupefidele::class, mappedBy="fidele")
     */
    private $groupefideles;

    /**
     * @ORM\OneToMany(targetEntity=Departementfidele::class, mappedBy="fidele")
     */
    private $departementfideles;

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
     * @ORM\ManyToOne(targetEntity=Bapteme::class, inversedBy="fidele", cascade ={"persist", "remove"})
     */
    private $bapteme;

    /**
     * @ORM\OneToMany(targetEntity=Dime::class, mappedBy="fidele")
     */
    private $dimes;

    /**
     * @ORM\OneToMany(targetEntity=Actiongrace::class, mappedBy="fidele")
     */
    private $actiongraces;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoFile;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="fideles")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Ethnie::class, inversedBy="fidele")
     */
    private $ethnie;

    /**
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="fidele")
     */
    private $famille;



    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $sexe;



    /**
     *
     * @ORM\ManyToOne(targetEntity=Quartier::class, inversedBy="fidele")
     * @Groups("public")    
     */
    private $quartier;

    /**
     * @ORM\OneToMany(targetEntity=Fidelecotiser::class, mappedBy="fidele")
     */
    private $fidelecotisers;

    /**
     * @ORM\OneToMany(targetEntity=Recommandation::class, mappedBy="fidele")
     */
    private $recommandations;

   

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @Gedmo\Slug(fields={"nomfidele"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Culte::class, mappedBy="messager")
     */
    private $cultes;

    /**
     * @ORM\OneToMany(targetEntity=Culte::class, mappedBy="dirigeant")
     */
    private $dirigeant;

   

  

       /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="fideles")
     */
    private $niveau;

    /**
     * @ORM\OneToMany(targetEntity=Mariage::class, mappedBy="epouxmembre")
      *@ORM\JoinTable(name="mariage")
     */
    private $epouxmembre;

    /**
     * @ORM\JoinTable(name="mariage")
     * @ORM\OneToMany(targetEntity=Mariage::class, mappedBy="epousemembre")
     */
    private $epousemembre;


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datemariage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pasteurmariage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieumariage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nommariage;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="fideles")
     */
    private $commune;

    /**
     * @ORM\OneToMany(targetEntity=Cotiserzone::class, mappedBy="fidele")
     */
    private $cotiserzones;

    /**
     * @ORM\OneToMany(targetEntity=Seancezone::class, mappedBy="fidele")
     */
    private $seancezones;

    /**
     * @ORM\OneToMany(targetEntity=Presencecellule::class, mappedBy="fidele")
     */
    private $presencecellules;

    /**
     * @ORM\OneToMany(targetEntity=Presencegroupe::class, mappedBy="fidele")
     */
    private $presencegroupes;

    /**
     * @ORM\OneToMany(targetEntity=Presencedepartement::class, mappedBy="fidele")
     */
    private $presencedepartements;

    /**
     * @ORM\OneToMany(targetEntity=Presencezone::class, mappedBy="fidele")
     */
    private $presencezones;

    /**
     * @ORM\OneToMany(targetEntity=Presencefamille::class, mappedBy="fidele")
     */
    private $presencefamilles;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisation::class, mappedBy="fidele")
     */
    private $detailcotisations;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationzone::class, mappedBy="fidele")
     */
    private $detailcotisationzones;

    /**
     * @ORM\OneToMany(targetEntity=Detailcotisationcellule::class, mappedBy="fidele")
     */
    private $detailcotisationcellules;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $habitation;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $vieseul;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $langue;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $choiculte;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $cultefamille;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $priere;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $lecture;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $temoignage;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $bibleformation;

    /**
     * @ORM\OneToMany(targetEntity=Livremembre::class, mappedBy="fidele")
     */
    private $livremembres;

    /**
     * @ORM\OneToMany(targetEntity=Cartesocial::class, mappedBy="fidele")
     */
    private $cartesocials;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $etatparent;

    /**
     * @ORM\OneToMany(targetEntity=Cartemembre::class, mappedBy="fidele")
     */
    private $cartemembres;

    /**
     * @ORM\OneToMany(targetEntity=Detailsociale::class, mappedBy="fidele")
     */
    private $detailsociales;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $etatvieparent;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $situation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $handicap;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datearriver;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatmariage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maladie;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $urgence;

    /**
     * @ORM\OneToMany(targetEntity=Invite::class, mappedBy="fidele")
     */
    private $invites;

    /**
     * @ORM\OneToMany(targetEntity=Visite::class, mappedBy="receptionpar")
     */
    private $visites;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="fideles")
     */
    private $eglise;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $permis;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $emploi;


    /**
     * @ORM\OneToMany(targetEntity=Pastorale::class, mappedBy="pasteur1")
     */
    private $pastorales;

    /**
     * @ORM\OneToMany(targetEntity=Presencepastorale::class, mappedBy="fidele")
     */
    private $presencepastorales;

    /**
     * @ORM\OneToMany(targetEntity=Discipline::class, mappedBy="fidele")
     */
    private $disciplines;

  

    /**
     * @ORM\OneToMany(targetEntity=Conge::class, mappedBy="fidele")
     */
    private $conges;

   

    /**
     * @ORM\ManyToMany(targetEntity=Nommination::class, mappedBy="fidele")
     */
    private $nomminations;

    /**
     * @ORM\ManyToOne(targetEntity=Fonction::class, inversedBy="fideles")
     */
    private $fonction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationalite;

    /**
     * @ORM\OneToMany(targetEntity=Visite2::class, mappedBy="fidele")
     */
    private $visite2s;

    /**
     * @ORM\OneToOne(targetEntity=Fiancaille::class, mappedBy="fiancemembre")
     */
    private ?Fiancaille $fiancailleFiance = null;

    /**
     * @ORM\OneToOne(targetEntity=Fiancaille::class, mappedBy="fianceemembre")
     */
    private ?Fiancaille $fiancailleFiancee = null;

     

        /**
         * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="peremembre")
         */
        private Collection $peremembre;

        /**
         * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="merembre")
         */
        private Collection $merembre;

  
    
   /**
     * @ORM\ManyToOne(targetEntity=Fidele::class)
     * @ORM\JoinColumn(name="pasteur1_id", referencedColumnName="id", nullable=true)
     */
    private $pasteur1;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class)
     * @ORM\JoinColumn(name="pasteur2_id", referencedColumnName="id", nullable=true)
     */
    private $pasteur2;

 
            /**
         * @ORM\OneToMany(targetEntity=Naissance::class, mappedBy="perenaisse")
         */
        private Collection $perenaisse;

        /**
         * @ORM\OneToMany(targetEntity=Naissance::class, mappedBy="merenaisse")
         */
        private Collection $merenaisse;

        /**
         * @ORM\OneToMany(targetEntity=Pastorale::class, mappedBy="fidele1")
         */
        private $pastoralesCommeFidele1;

        /**
         * @ORM\OneToMany(targetEntity=Pastorale::class, mappedBy="fidele2")
         */
        private $pastoralesCommeFidele2;
    

    public function __construct() {
        $this->pastoralesCommeFidele1 = new ArrayCollection();
        $this->pastoralesCommeFidele2 = new ArrayCollection();
        $this->groupefideles = new ArrayCollection();
        $this->departementfideles = new ArrayCollection();
        $this->dimes = new ArrayCollection();
        $this->actiongraces = new ArrayCollection();
        $this->pastorales = new ArrayCollection();
        $this->perenaisse = new ArrayCollection();
        $this->merenaisse = new ArrayCollection();
        $this->fidelecotisers = new ArrayCollection();
        $this->scenes = new ArrayCollection();
        $this->recommandations = new ArrayCollection();
        $this->fideleRepo = \App\Repository\FideleRepository::class;
        $this->cultes = new ArrayCollection();
        $this->dirigeant = new ArrayCollection();
        $this->peremembre = new ArrayCollection();
        $this->epouxmembre = new ArrayCollection();
        $this->epousemembre = new ArrayCollection();
        $this->fiancemembre = new ArrayCollection();
        $this->fianceemembre = new ArrayCollection();
    
        $this->cotiserzones = new ArrayCollection();
        $this->seancezones = new ArrayCollection();
        $this->presencecellules = new ArrayCollection();
        $this->presencegroupes = new ArrayCollection();
        $this->presencedepartements = new ArrayCollection();
        $this->presencezones = new ArrayCollection();
        $this->presencefamilles = new ArrayCollection();
        $this->detailcotisations = new ArrayCollection();
        $this->detailcotisationzones = new ArrayCollection();
        $this->detailcotisationcellules = new ArrayCollection();
        $this->livremembres = new ArrayCollection();
        $this->cartesocials = new ArrayCollection();
        $this->cartemembres = new ArrayCollection();
        $this->detailsociales = new ArrayCollection();
        $this->invites = new ArrayCollection();
        $this->visites = new ArrayCollection();
        //$this->scenes = new ArrayCollection();
        $this->presencepastorales = new ArrayCollection();
        $this->disciplines = new ArrayCollection();
        $this->conges = new ArrayCollection();
        $this->nomminations = new ArrayCollection();
        $this->visite2s = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTypefidele(): ?string {
        return $this->typefidele;
    }

    public function setTypefidele(string $typefidele): self {
        $this->typefidele = $typefidele;

        return $this;
    }

    public function getNomfidele(): ?string {
        return $this->nomfidele;
    }

    public function setNomfidele(string $nomfidele): self {
        $this->nomfidele = $nomfidele;

        return $this;
    }

    public function getContact1(): ?string {
        return $this->contact1;
    }

    public function setContact1(?string $contact1): self {
        $this->contact1 = $contact1;

        return $this;
    }

    public function getContactwhatssap(): ?string {
        return $this->contactwhatssap;
    }

    public function setContactwhatssap(?string $contactwhatssap): self {
        $this->contactwhatssap = $contactwhatssap;

        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): self {
        $this->email = $email;

        return $this;
    }

    public function getNbrenfant(): ?int {
        return $this->nbrenfant;
    }

    public function setNbrenfant(?int $nbrenfant): self {
        $this->nbrenfant = $nbrenfant;

        return $this;
    }

    public function getStatutmatri(): ?string {
        return $this->statutmatri;
    }

    public function setStatutmatri(?string $statutmatri): self {
        $this->statutmatri = $statutmatri;

        return $this;
    }

    public function getDatenaiss(): ?\DateTimeInterface {
        return $this->datenaiss;
    }

    public function setDatenaiss(?\DateTimeInterface $datenaiss): self {
        $this->datenaiss = $datenaiss;

        return $this;
    }

    public function getNumpiece(): ?string {
        return $this->numpiece;
    }

    public function setNumpiece(?string $numpiece): self {
        $this->numpiece = $numpiece;

        return $this;
    }

    public function getTypepiece(): ?string {
        return $this->typepiece;
    }

    public function setTypepiece(?string $typepiece): self {
        $this->typepiece = $typepiece;

        return $this;
    }

    public function getComptefacebook(): ?string {
        return $this->comptefacebook;
    }

    public function setComptefacebook(?string $comptefacebook): self {
        $this->comptefacebook = $comptefacebook;

        return $this;
    }

    public function getDomaineactivite(): ?string {
        return $this->domaineactivite;
    }

    public function setDomaineactivite(?string $domaineactivite): self {
        $this->domaineactivite = $domaineactivite;

        return $this;
    }

    public function getProfession(): ?string {
        return $this->profession;
    }

    public function setProfession(?string $profession): self {
        $this->profession = $profession;

        return $this;
    }

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self {
        $this->photo = $photo;

        return $this;
    }

    public function getGroupesang(): ?string {
        return $this->groupesang;
    }

    public function setGroupesang(?string $groupesang): self {
        $this->groupesang = $groupesang;

        return $this;
    }

    public function getDateconversion(): ?\DateTimeInterface {
        return $this->dateconversion;
    }

    public function setDateconversion(?\DateTimeInterface $dateconversion): self {
        $this->dateconversion = $dateconversion;

        return $this;
    }

    public function getStutbapteme(): ?string {
        return $this->stutbapteme;
    }

    public function setStutbapteme(?string $stutbapteme): self {
        $this->stutbapteme = $stutbapteme;

        return $this;
    }

    public function getDatebapteme(): ?\DateTimeInterface {
        return $this->datebapteme;
    }

    public function setDatebapteme(?\DateTimeInterface $datebapteme): self {
        $this->datebapteme = $datebapteme;

        return $this;
    }

    public function getLieubapteme(): ?string {
        return $this->lieubapteme;
    }

    public function setLieubapteme(?string $lieubapteme): self {
        $this->lieubapteme = $lieubapteme;

        return $this;
    }

    public function getPasteurbapteme(): ?string {
        return $this->pasteurbapteme;
    }

    public function setPasteurbapteme(?string $pasteurbapteme): self {
        $this->pasteurbapteme = $pasteurbapteme;

        return $this;
    }

    public function getAnciennecommunaute(): ?string {
        return $this->anciennecommunaute;
    }

    public function setAnciennecommunaute(?string $anciennecommunaute): self {
        $this->anciennecommunaute = $anciennecommunaute;

        return $this;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function setCode(?string $code): self {
        $this->code = $code;

        return $this;
    }

    public function getCellule(): ?Cellule {
        return $this->cellule;
    }

    public function setCellule(?Cellule $cellule): self {
        $this->cellule = $cellule;

        return $this;
    }

        public function getNiveau(): ?Niveau {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self {
        $this->niveau = $niveau;

        return $this;
    }

    public function getEtatfidele(): ?bool {
        return $this->etatfidele;
    }

    public function setEtatfidele(?bool $etatfidele): self {
        $this->etatfidele = $etatfidele;

        return $this;
    }

    /**
     * @return Collection|Groupefidele[]
     */
    public function getGroupefideles(): Collection {
        return $this->groupefideles;
    }

    public function addGroupefidele(Groupefidele $groupefidele): self {
        if (!$this->groupefideles->contains($groupefidele)) {
            $this->groupefideles[] = $groupefidele;
            $groupefidele->setFidele($this);
        }

        return $this;
    }

    public function removeGroupefidele(Groupefidele $groupefidele): self {
        if ($this->groupefideles->removeElement($groupefidele)) {
            // set the owning side to null (unless already changed)
            if ($groupefidele->getFidele() === $this) {
                $groupefidele->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Departementfidele[]
     */
    public function getDepartementfideles(): Collection {
        return $this->departementfideles;
    }

    public function addDepartementfidele(Departementfidele $departementfidele): self {
        if (!$this->departementfideles->contains($departementfidele)) {
            $this->departementfideles[] = $departementfidele;
            $departementfidele->setFidele($this);
        }

        return $this;
    }

    public function removeDepartementfidele(Departementfidele $departementfidele): self {
        if ($this->departementfideles->removeElement($departementfidele)) {
            // set the owning side to null (unless already changed)
            if ($departementfidele->getFidele() === $this) {
                $departementfidele->setFidele(null);
            }
        }

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

    public function getBapteme(): ?Bapteme {
        return $this->bapteme;
    }

    public function setBapteme(?Bapteme $bapteme): self {
        $this->bapteme = $bapteme;

        return $this;
    }

    /**
     * @return Collection|Dime[]
     */
    public function getDimes(): Collection {
        return $this->dimes;
    }

    public function addDime(Dime $dime): self {
        if (!$this->dimes->contains($dime)) {
            $this->dimes[] = $dime;
            $dime->setFidele($this);
        }

        return $this;
    }

    public function removeDime(Dime $dime): self {
        if ($this->dimes->removeElement($dime)) {
            // set the owning side to null (unless already changed)
            if ($dime->getFidele() === $this) {
                $dime->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Actiongrace[]
     */
    public function getActiongraces(): Collection {
        return $this->actiongraces;
    }

    public function addActiongrace(Actiongrace $actiongrace): self {
        if (!$this->actiongraces->contains($actiongrace)) {
            $this->actiongraces[] = $actiongrace;
            $actiongrace->setFidele($this);
        }

        return $this;
    }

    public function removeActiongrace(Actiongrace $actiongrace): self {
        if ($this->actiongraces->removeElement($actiongrace)) {
            // set the owning side to null (unless already changed)
            if ($actiongrace->getFidele() === $this) {
                $actiongrace->setFidele(null);
            }
        }

        return $this;
    }

  
    public function getPhotoFile(): ?string {
        return $this->photoFile;
    }

    public function setPhotoFile(?string $photoFile): self {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getZone(): ?Zone {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self {
        $this->zone = $zone;

        return $this;
    }

    public function getEthnie(): ?Ethnie {
        return $this->ethnie;
    }

    public function setEthnie(?Ethnie $ethnie): self {
        $this->ethnie = $ethnie;

        return $this;
    }

    public function getFamille(): ?Famille {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self {
        $this->famille = $famille;

        return $this;
    }

    public function __toString() {
        return $this->getNomfidele();
    }

//    /**
//     * @return Collection|Fideleenfant[]
//     */
//    public function getFideleenfants(): Collection {
//        return $this->fideleenfants;
//    }
//
//    public function addFideleenfant(Fideleenfant $fideleenfant): self {
//        if (!$this->fideleenfants->contains($fideleenfant)) {
//            $this->fideleenfants[] = $fideleenfant;
//            $fideleenfant->setFidele($this);
//        }
//
//        return $this;
//    }
//
//    public function removeFideleenfant(Fideleenfant $fideleenfant): self {
//        if ($this->fideleenfants->removeElement($fideleenfant)) {
//            // set the owning side to null (unless already changed)
//            if ($fideleenfant->getFidele() === $this) {
//                $fideleenfant->setFidele(null);
//            }
//        }
//
//        return $this;
//    }

    
    public function getSexe(): ?string {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self {
        $this->sexe = $sexe;

        return $this;
    }

  

    public function getQuartier(): ?Quartier {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * @return Collection|Fidelecotiser[]
     */
    public function getFidelecotisers(): Collection {
        return $this->fidelecotisers;
    }

    public function addFidelecotiser(Fidelecotiser $fidelecotiser): self {
        if (!$this->fidelecotisers->contains($fidelecotiser)) {
            $this->fidelecotisers[] = $fidelecotiser;
            $fidelecotiser->setFidele($this);
        }

        return $this;
    }

    public function removeFidelecotiser(Fidelecotiser $fidelecotiser): self {
        if ($this->fidelecotisers->removeElement($fidelecotiser)) {
            // set the owning side to null (unless already changed)
            if ($fidelecotiser->getFidele() === $this) {
                $fidelecotiser->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Recommandation[]
     */
    public function getRecommandations(): Collection {
        return $this->recommandations;
    }

    public function addRecommandation(Recommandation $recommandation): self {
        if (!$this->recommandations->contains($recommandation)) {
            $this->recommandations[] = $recommandation;
            $recommandation->setFidele($this);
        }

        return $this;
    }

    public function removeRecommandation(Recommandation $recommandation): self {
        if ($this->recommandations->removeElement($recommandation)) {
            // set the owning side to null (unless already changed)
            if ($recommandation->getFidele() === $this) {
                $recommandation->setFidele(null);
            }
        }

        return $this;
    }



    public function getAge(): ?int {
        return $this->age;
    }

    public function setAge(?int $age): self {
        $this->age = $age;

        return $this;
    }

    public function getSlug(): ?string {
        return $this->slug;
    }

    public function setSlug(?string $slug): self {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Culte[]
     */
    public function getCultes(): Collection {
        return $this->cultes;
    }

    public function addCulte(Culte $culte): self {
        if (!$this->cultes->contains($culte)) {
            $this->cultes[] = $culte;
            $culte->setMessager($this);
        }

        return $this;
    }

    public function removeCulte(Culte $culte): self {
        if ($this->cultes->removeElement($culte)) {
            // set the owning side to null (unless already changed)
            if ($culte->getMessager() === $this) {
                $culte->setMessager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Culte[]
     */
    public function getDirigeant(): Collection {
        return $this->dirigeant;
    }

    public function addDirigeant(Culte $dirigeant): self {
        if (!$this->dirigeant->contains($dirigeant)) {
            $this->dirigeant[] = $dirigeant;
            $dirigeant->setDirigeant($this);
        }

        return $this;
    }

    public function removeDirigeant(Culte $dirigeant): self {
        if ($this->dirigeant->removeElement($dirigeant)) {
            // set the owning side to null (unless already changed)
            if ($dirigeant->getDirigeant() === $this) {
                $dirigeant->setDirigeant(null);
            }
        }

        return $this;
    }


   /**
 * @return Collection|Enfant[]
 */
public function getPeremembre(): Collection
{
    return $this->peremembre;
}

public function addPeremembre(Enfant $enfant): self
{
    if (!$this->peremembre->contains($enfant)) {
        $this->peremembre[] = $enfant;
        $enfant->setPeremembre($this);
    }

    return $this;
}

public function removePeremembre(Enfant $enfant): self
{
    if ($this->peremembre->removeElement($enfant)) {
        if ($enfant->getPeremembre() === $this) {
            $enfant->setPeremembre(null);
        }
    }

    return $this;
}

/**
 * @return Collection|Enfant[]
 */
public function getMeremembre(): Collection
{
    return $this->meremembre;
}

public function addMeremembre(Enfant $enfant): self
{
    if (!$this->meremembre->contains($enfant)) {
        $this->meremembre[] = $enfant;
        $enfant->setMerembre($this);
    }

    return $this;
}

public function removeMeremembre(Enfant $enfant): self
{
    if ($this->meremembre->removeElement($enfant)) {
        if ($enfant->getMerembre() === $this) {
            $enfant->setMerembre(null);
        }
    }

    return $this;
}

    /**
     * @return Collection|Mariage[]
     */
    public function getEpouxmembre(): Collection {
        return $this->epouxmembre;
    }

    public function addEpouxmembre(Mariage $epouxmembre): self {
        if (!$this->epouxmembre->contains($epouxmembre)) {
            $this->epouxmembre[] = $epouxmembre;
            $epouxmembre->setEpouxmembre($this);
        }

        return $this;
    }

    public function removeEpouxmembre(Mariage $epouxmembre): self {
        if ($this->epouxmembre->removeElement($epouxmembre)) {
            // set the owning side to null (unless already changed)
            if ($epouxmembre->getEpouxmembre() === $this) {
                $epouxmembre->setEpouxmembre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Mariage[]
     */
    public function getEpousemembre(): Collection {
        return $this->epousemembre;
    }

    public function addEpousemembre(Mariage $epousemembre): self {
        if (!$this->epousemembre->contains($epousemembre)) {
            $this->epousemembre[] = $epousemembre;
            $epousemembre->setEpousemembre($this);
        }

        return $this;
    }

    public function removeEpousemembre(Mariage $epousemembre): self {
        if ($this->epousemembre->removeElement($epousemembre)) {
            // set the owning side to null (unless already changed)
            if ($epousemembre->getEpousemembre() === $this) {
                $epousemembre->setEpousemembre(null);
            }
        }

        return $this;
    }

 

    public function getDatemariage(): ?\DateTimeInterface {
        return $this->datemariage;
    }

    public function setDatemariage(?\DateTimeInterface $datemariage): self {
        $this->datemariage = $datemariage;

        return $this;
    }

    public function getPasteurmariage(): ?string {
        return $this->pasteurmariage;
    }

    public function setPasteurmariage(?string $pasteurmariage): self {
        $this->pasteurmariage = $pasteurmariage;

        return $this;
    }

    public function getLieumariage(): ?string {
        return $this->lieumariage;
    }

    public function setLieumariage(?string $lieumariage): self {
        $this->lieumariage = $lieumariage;

        return $this;
    }

    public function getNommariage(): ?string {
        return $this->nommariage;
    }

    public function setNommariage(?string $nommariage): self {
        $this->nommariage = $nommariage;

        return $this;
    }

    public function getCommune(): ?Commune {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self {
        $this->commune = $commune;

        return $this;
    }

    /**
     * @return Collection<int, Cotiserzone>
     */
    public function getCotiserzones(): Collection {
        return $this->cotiserzones;
    }

    public function addCotiserzone(Cotiserzone $cotiserzone): self {
        if (!$this->cotiserzones->contains($cotiserzone)) {
            $this->cotiserzones[] = $cotiserzone;
            $cotiserzone->setFidele($this);
        }

        return $this;
    }

    public function removeCotiserzone(Cotiserzone $cotiserzone): self {
        if ($this->cotiserzones->removeElement($cotiserzone)) {
            // set the owning side to null (unless already changed)
            if ($cotiserzone->getFidele() === $this) {
                $cotiserzone->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Seancezone>
     */
    public function getSeancezones(): Collection {
        return $this->seancezones;
    }

    public function addSeancezone(Seancezone $seancezone): self {
        if (!$this->seancezones->contains($seancezone)) {
            $this->seancezones[] = $seancezone;
            $seancezone->setFidele($this);
        }

        return $this;
    }

    public function removeSeancezone(Seancezone $seancezone): self {
        if ($this->seancezones->removeElement($seancezone)) {
            // set the owning side to null (unless already changed)
            if ($seancezone->getFidele() === $this) {
                $seancezone->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencecellule>
     */
    public function getPresencecellules(): Collection {
        return $this->presencecellules;
    }

    public function addPresencecellule(Presencecellule $presencecellule): self {
        if (!$this->presencecellules->contains($presencecellule)) {
            $this->presencecellules[] = $presencecellule;
            $presencecellule->setFidele($this);
        }

        return $this;
    }

    public function removePresencecellule(Presencecellule $presencecellule): self {
        if ($this->presencecellules->removeElement($presencecellule)) {
            // set the owning side to null (unless already changed)
            if ($presencecellule->getFidele() === $this) {
                $presencecellule->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencegroupe>
     */
    public function getPresencegroupes(): Collection {
        return $this->presencegroupes;
    }

    public function addPresencegroupe(Presencegroupe $presencegroupe): self {
        if (!$this->presencegroupes->contains($presencegroupe)) {
            $this->presencegroupes[] = $presencegroupe;
            $presencegroupe->setFidele($this);
        }

        return $this;
    }

    public function removePresencegroupe(Presencegroupe $presencegroupe): self {
        if ($this->presencegroupes->removeElement($presencegroupe)) {
            // set the owning side to null (unless already changed)
            if ($presencegroupe->getFidele() === $this) {
                $presencegroupe->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencedepartement>
     */
    public function getPresencedepartements(): Collection {
        return $this->presencedepartements;
    }

    public function addPresencedepartement(Presencedepartement $presencedepartement): self {
        if (!$this->presencedepartements->contains($presencedepartement)) {
            $this->presencedepartements[] = $presencedepartement;
            $presencedepartement->setFidele($this);
        }

        return $this;
    }

    public function removePresencedepartement(Presencedepartement $presencedepartement): self {
        if ($this->presencedepartements->removeElement($presencedepartement)) {
            // set the owning side to null (unless already changed)
            if ($presencedepartement->getFidele() === $this) {
                $presencedepartement->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencezone>
     */
    public function getPresencezones(): Collection {
        return $this->presencezones;
    }

    public function addPresencezone(Presencezone $presencezone): self {
        if (!$this->presencezones->contains($presencezone)) {
            $this->presencezones[] = $presencezone;
            $presencezone->setFidele($this);
        }

        return $this;
    }

    public function removePresencezone(Presencezone $presencezone): self {
        if ($this->presencezones->removeElement($presencezone)) {
            // set the owning side to null (unless already changed)
            if ($presencezone->getFidele() === $this) {
                $presencezone->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencefamille>
     */
    public function getPresencefamilles(): Collection {
        return $this->presencefamilles;
    }

    public function addPresencefamille(Presencefamille $presencefamille): self {
        if (!$this->presencefamilles->contains($presencefamille)) {
            $this->presencefamilles[] = $presencefamille;
            $presencefamille->setFidele($this);
        }

        return $this;
    }

    public function removePresencefamille(Presencefamille $presencefamille): self {
        if ($this->presencefamilles->removeElement($presencefamille)) {
            // set the owning side to null (unless already changed)
            if ($presencefamille->getFidele() === $this) {
                $presencefamille->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisation>
     */
    public function getDetailcotisations(): Collection {
        return $this->detailcotisations;
    }

    public function addDetailcotisation(Detailcotisation $detailcotisation): self {
        if (!$this->detailcotisations->contains($detailcotisation)) {
            $this->detailcotisations[] = $detailcotisation;
            $detailcotisation->setFidele($this);
        }

        return $this;
    }

    public function removeDetailcotisation(Detailcotisation $detailcotisation): self {
        if ($this->detailcotisations->removeElement($detailcotisation)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisation->getFidele() === $this) {
                $detailcotisation->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisationzone>
     */
    public function getDetailcotisationzones(): Collection {
        return $this->detailcotisationzones;
    }

    public function addDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self {
        if (!$this->detailcotisationzones->contains($detailcotisationzone)) {
            $this->detailcotisationzones[] = $detailcotisationzone;
            $detailcotisationzone->setFidele($this);
        }

        return $this;
    }

    public function removeDetailcotisationzone(Detailcotisationzone $detailcotisationzone): self {
        if ($this->detailcotisationzones->removeElement($detailcotisationzone)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationzone->getFidele() === $this) {
                $detailcotisationzone->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailcotisationcellule>
     */
    public function getDetailcotisationcellules(): Collection {
        return $this->detailcotisationcellules;
    }

    public function addDetailcotisationcellule(Detailcotisationcellule $detailcotisationcellule): self {
        if (!$this->detailcotisationcellules->contains($detailcotisationcellule)) {
            $this->detailcotisationcellules[] = $detailcotisationcellule;
            $detailcotisationcellule->setFidele($this);
        }

        return $this;
    }

    public function removeDetailcotisationcellule(Detailcotisationcellule $detailcotisationcellule): self {
        if ($this->detailcotisationcellules->removeElement($detailcotisationcellule)) {
            // set the owning side to null (unless already changed)
            if ($detailcotisationcellule->getFidele() === $this) {
                $detailcotisationcellule->setFidele(null);
            }
        }

        return $this;
    }

    public function getHabitation(): ?string {
        return $this->habitation;
    }

    public function setHabitation(?string $habitation): self {
        $this->habitation = $habitation;

        return $this;
    }

    public function getVieseul(): ?string {
        return $this->vieseul;
    }

    public function setVieseul(?string $vieseul): self {
        $this->vieseul = $vieseul;

        return $this;
    }

    public function getLangue(): ?string {
        return $this->langue;
    }

    public function setLangue(?string $langue): self {
        $this->langue = $langue;

        return $this;
    }

    public function getChoiculte(): ?string {
        return $this->choiculte;
    }

    public function setChoiculte(?string $choiculte): self {
        $this->choiculte = $choiculte;

        return $this;
    }

    public function getCultefamille(): ?string {
        return $this->cultefamille;
    }

    public function setCultefamille(?string $cultefamille): self {
        $this->cultefamille = $cultefamille;

        return $this;
    }

    public function getPriere(): ?string {
        return $this->priere;
    }

    public function setPriere(?string $priere): self {
        $this->priere = $priere;

        return $this;
    }

    public function getLecture(): ?string {
        return $this->lecture;
    }

    public function setLecture(?string $lecture): self {
        $this->lecture = $lecture;

        return $this;
    }

    public function getTemoignage(): ?string {
        return $this->temoignage;
    }

    public function setTemoignage(?string $temoignage): self {
        $this->temoignage = $temoignage;

        return $this;
    }

    public function getBibleformation(): ?string {
        return $this->bibleformation;
    }

    public function setBibleformation(?string $bibleformation): self {
        $this->bibleformation = $bibleformation;

        return $this;
    }

    /**
     * @return Collection<int, Livremembre>
     */
    public function getLivremembres(): Collection {
        return $this->livremembres;
    }

    public function addLivremembre(Livremembre $livremembre): self {
        if (!$this->livremembres->contains($livremembre)) {
            $this->livremembres[] = $livremembre;
            $livremembre->setFidele($this);
        }

        return $this;
    }

    public function removeLivremembre(Livremembre $livremembre): self {
        if ($this->livremembres->removeElement($livremembre)) {
            // set the owning side to null (unless already changed)
            if ($livremembre->getFidele() === $this) {
                $livremembre->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cartesocial>
     */
    public function getCartesocials(): Collection {
        return $this->cartesocials;
    }

    public function addCartesocial(Cartesocial $cartesocial): self {
        if (!$this->cartesocials->contains($cartesocial)) {
            $this->cartesocials[] = $cartesocial;
            $cartesocial->setFidele($this);
        }

        return $this;
    }

    public function removeCartesocial(Cartesocial $cartesocial): self {
        if ($this->cartesocials->removeElement($cartesocial)) {
            // set the owning side to null (unless already changed)
            if ($cartesocial->getFidele() === $this) {
                $cartesocial->setFidele(null);
            }
        }

        return $this;
    }

    public function getEtatparent(): ?string {
        return $this->etatparent;
    }

    public function setEtatparent(?string $etatparent): self {
        $this->etatparent = $etatparent;

        return $this;
    }

    /**
     * @return Collection<int, Cartemembre>
     */
    public function getCartemembres(): Collection {
        return $this->cartemembres;
    }

    public function addCartemembre(Cartemembre $cartemembre): self {
        if (!$this->cartemembres->contains($cartemembre)) {
            $this->cartemembres[] = $cartemembre;
            $cartemembre->setFidele($this);
        }

        return $this;
    }

    public function removeCartemembre(Cartemembre $cartemembre): self {
        if ($this->cartemembres->removeElement($cartemembre)) {
            // set the owning side to null (unless already changed)
            if ($cartemembre->getFidele() === $this) {
                $cartemembre->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Detailsociale>
     */
    public function getDetailsociales(): Collection {
        return $this->detailsociales;
    }

    public function addDetailsociale(Detailsociale $detailsociale): self {
        if (!$this->detailsociales->contains($detailsociale)) {
            $this->detailsociales[] = $detailsociale;
            $detailsociale->setFidele($this);
        }

        return $this;
    }

    public function removeDetailsociale(Detailsociale $detailsociale): self {
        if ($this->detailsociales->removeElement($detailsociale)) {
            // set the owning side to null (unless already changed)
            if ($detailsociale->getFidele() === $this) {
                $detailsociale->setFidele(null);
            }
        }

        return $this;
    }

    public function getEtatvieparent(): ?string {
        return $this->etatvieparent;
    }

    public function setEtatvieparent(?string $etatvieparent): self {
        $this->etatvieparent = $etatvieparent;

        return $this;
    }

    public function getSituation(): ?string {
        return $this->situation;
    }

    public function setSituation(?string $situation): self {
        $this->situation = $situation;

        return $this;
    }

    public function getHandicap(): ?string {
        return $this->handicap;
    }

    public function setHandicap(?string $handicap): self {
        $this->handicap = $handicap;

        return $this;
    }

    public function getDatearriver(): ?\DateTimeInterface {
        return $this->datearriver;
    }

    public function setDatearriver(?\DateTimeInterface $datearriver): self {
        $this->datearriver = $datearriver;

        return $this;
    }

    public function getEtatmariage(): ?bool {
        return $this->etatmariage;
    }

    public function setEtatmariage(?bool $etatmariage): self {
        $this->etatmariage = $etatmariage;

        return $this;
    }

    public function getMaladie(): ?string {
        return $this->maladie;
    }

    public function setMaladie(?string $maladie): self {
        $this->maladie = $maladie;

        return $this;
    }

    public function getUrgence(): ?string {
        return $this->urgence;
    }

    public function setUrgence(?string $urgence): self {
        $this->urgence = $urgence;

        return $this;
    }

    /**
     * @return Collection<int, Invite>
     */
    public function getInvites(): Collection {
        return $this->invites;
    }

    public function addInvite(Invite $invite): self {
        if (!$this->invites->contains($invite)) {
            $this->invites[] = $invite;
            $invite->setFidele($this);
        }

        return $this;
    }

    public function removeInvite(Invite $invite): self {
        if ($this->invites->removeElement($invite)) {
            // set the owning side to null (unless already changed)
            if ($invite->getFidele() === $this) {
                $invite->setFidele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Visite>
     */
    public function getVisites(): Collection {
        return $this->visites;
    }

    public function addVisite(Visite $visite): self {
        if (!$this->visites->contains($visite)) {
            $this->visites[] = $visite;
            $visite->setReceptionpar($this);
        }

        return $this;
    }

    public function removeVisite(Visite $visite): self {
        if ($this->visites->removeElement($visite)) {
            // set the owning side to null (unless already changed)
            if ($visite->getReceptionpar() === $this) {
                $visite->setReceptionpar(null);
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

//    public function getEmploi(): ?string
//    {
//        return $this->emploi;
//    }
//
//    public function setEmploi(?string $emploi): self
//    {
//        $this->emploi = $emploi;
//
//        return $this;
//    }

    public function getPermis(): ?string {
        return $this->permis;
    }

    public function setPermis(?string $permis): self {
        $this->permis = $permis;

        return $this;
    }

    public function getEmploi(): ?string {
        return $this->emploi;
    }

    public function setEmploi(?string $emploi): self {
        $this->emploi = $emploi;

        return $this;
    }


    /**
     * @return Collection<int, Pastorale>
     */
    public function getPastorales(): Collection {
        return $this->pastorales;
    }

    public function addPastorale(Pastorale $pastorale): self {
        if (!$this->pastorales->contains($pastorale)) {
            $this->pastorales[] = $pastorale;
            $pastorale->setPasteur1($this);
        }

        return $this;
    }

    public function removePastorale(Pastorale $pastorale): self {
        if ($this->pastorales->removeElement($pastorale)) {
            // set the owning side to null (unless already changed)
            if ($pastorale->getPasteur1() === $this) {
                $pastorale->setPasteur1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presencepastorale>
     */
    public function getPresencepastorales(): Collection {
        return $this->presencepastorales;
    }

    public function addPresencepastorale(Presencepastorale $presencepastorale): self {
        if (!$this->presencepastorales->contains($presencepastorale)) {
            $this->presencepastorales[] = $presencepastorale;
            $presencepastorale->setFidele($this);
        }

        return $this;
    }

    public function removePresencepastorale(Presencepastorale $presencepastorale): self {
        if ($this->presencepastorales->removeElement($presencepastorale)) {
            // set the owning side to null (unless already changed)
            if ($presencepastorale->getFidele() === $this) {
                $presencepastorale->setFidele(null);
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
            $discipline->setFidele($this);
        }

        return $this;
    }

    public function removeDiscipline(Discipline $discipline): self
    {
        if ($this->disciplines->removeElement($discipline)) {
            // set the owning side to null (unless already changed)
            if ($discipline->getFidele() === $this) {
                $discipline->setFidele(null);
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
            $conge->setFidele($this);
        }

        return $this;
    }

    public function removeConge(Conge $conge): self
    {
        if ($this->conges->removeElement($conge)) {
            // set the owning side to null (unless already changed)
            if ($conge->getFidele() === $this) {
                $conge->setFidele(null);
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

    public function getFonction(): ?Fonction
    {
        return $this->fonction;
    }

    public function setFonction(?Fonction $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getEtude(): ?string
    {
        return $this->etude;
    }

    public function setEtude(?string $etude): self
    {
        $this->etude = $etude;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): self
    {
        $this->nationalite = $nationalite;

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
            $visite2->setFidele($this);
        }

        return $this;
    }

    public function removeVisite2(Visite2 $visite2): self
    {
        if ($this->visite2s->removeElement($visite2)) {
            // set the owning side to null (unless already changed)
            if ($visite2->getFidele() === $this) {
                $visite2->setFidele(null);
            }
        }

        return $this;
    }

/**
 * @return Collection|Naissance[]
 */
public function getPerenaisse(): Collection
{
    return $this->perenaisse;
}

public function addPerenaisse(Naissance $naissance): self
{
    if (!$this->perenaisse->contains($naissance)) {
        $this->perenaisse[] = $naissance;
        $naissance->setPerenaisse($this);
    }

    return $this;
}

public function removePerenaisse(Naissance $naissance): self
{
    if ($this->perenaisse->removeElement($naissance)) {
        if ($naissance->getPerenaisse() === $this) {
            $naissance->setPerenaisse(null);
        }
    }

    return $this;
}

/**
 * @return Collection|Naissance[]
 */
public function getMerenaisse(): Collection
{
    return $this->merenaisse;
}

public function addMerenaisse(Naissance $naissance): self
{
    if (!$this->merenaisse->contains($naissance)) {
        $this->merenaisse[] = $naissance;
        $naissance->setMerenaisse($this);
    }

    return $this;
}

public function removeMerenaisse(Naissance $naissance): self
{
    if ($this->merenaisse->removeElement($naissance)) {
        if ($naissance->getMerenaisse() === $this) {
            $naissance->setMerenaisse(null);
        }
    }

    return $this;
}

public function getPastoralesCommeFidele1(): Collection
{
    return $this->pastoralesCommeFidele1;
}

public function addPastoraleCommeFidele1(Pastorale $pastorale): self
{
    if (!$this->pastoralesCommeFidele1->contains($pastorale)) {
        $this->pastoralesCommeFidele1[] = $pastorale;
        $pastorale->setFidele1($this);
    }

    return $this;
}

public function removePastoraleCommeFidele1(Pastorale $pastorale): self
{
    if ($this->pastoralesCommeFidele1->removeElement($pastorale)) {
        if ($pastorale->getFidele1() === $this) {
            $pastorale->setFidele1(null);
        }
    }

    return $this;
}

public function getPastoralesCommeFidele2(): Collection
{
    return $this->pastoralesCommeFidele2;
}

public function addPastoraleCommeFidele2(Pastorale $pastorale): self
{
    if (!$this->pastoralesCommeFidele2->contains($pastorale)) {
        $this->pastoralesCommeFidele2[] = $pastorale;
        $pastorale->setFidele2($this);
    }

    return $this;
}

public function removePastoraleCommeFidele2(Pastorale $pastorale): self
{
    if ($this->pastoralesCommeFidele2->removeElement($pastorale)) {
        if ($pastorale->getFidele2() === $this) {
            $pastorale->setFidele2(null);
        }
    }

    return $this;
}


    /**
     * @return Collection<int, Scene>
     */
    public function getScenes(): Collection {
        return $this->scenes;
    }

    public function addScene(Scene $scene): self {
        if (!$this->scenes->contains($scene)) {
            $this->scenes[] = $scene;
            $scene->setPasteur1($this);
        }

        return $this;
    }

    public function removeScene(Scene $scene): self {
        if ($this->scenes->removeElement($scene)) {
            // set the owning side to null (unless already changed)
            if ($scene->getPasteur1() === $this) {
                $scene->setPasteur1(null);
            }
        }

        return $this;
    }

}
