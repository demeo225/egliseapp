<?php

namespace App\Entity;

use App\Repository\EnfantRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @ORM\Entity(repositoryClass=EnfantRepository::class)
 */
class Enfant extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $sexe;
    
       /**
     * @ORM\Column(type="string", length=16, nullable=true)
     *  @Groups("public")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $contact;

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
     * @ORM\Column(type="date", nullable=true)
     * @Groups("public")
     */
    private $datenaiss;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $lieunaiss;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups("public")
     */
    private $numpiece;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $comptefacebook;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     * @Groups("public")
     */
    private $niveauetude;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $classenfant;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups("public")
     */
    private $groupesang;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;



    /**
     * @ORM\ManyToOne(targetEntity=Ethnie::class, inversedBy="enfants")
     */
    private $ethnie;

    

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleteAt;

    /**
     * @ORM\OneToMany(targetEntity=Inscrire::class, mappedBy="enfant")
     */
    private $inscrires;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatenfant;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $photoFile;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     */
    private $eglise;


    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="enfant")
    
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="enfant")
     */
    private $famille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;
//
//    /**
//     * @ORM\OneToMany(targetEntity=Parentenfant::class, mappedBy="enfant")
//     */
//    private $parentenfants;

    /**
     * @Gedmo\Slug(fields={"nom"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="enfants")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="peremembre")
     */
    private $peremembre;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="meremembre")
     */
    private $merembre;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $pere;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $mere;



    /**
     * @ORM\Column(type="string", length=148, nullable=true)
     */
    private $lieuvivre;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $handicap;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $vieparent;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $situation;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $situationparent;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="enfants")
     */
    private $commune;

    /**
     * @ORM\ManyToOne(targetEntity=Quartier::class, inversedBy="enfants")
     
     */
    private $quartier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maladie;

    /**
     * @ORM\OneToMany(targetEntity=Detailenfantactivite::class, mappedBy="enfant")
     */
    private $detailenfantactivites;

    /**
     * @ORM\OneToMany(targetEntity=Presenceculteecodim::class, mappedBy="enfant")
     */
    private $presenceculteecodims;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationalite;

   
   

    public function __construct() {
        $this->inscrires = new ArrayCollection();
        $request = new Request();
        $this->request = $request;
        DateTime::class;
//        $this->parentenfants = new ArrayCollection();
$this->detailenfantactivites = new ArrayCollection();
$this->presenceculteecodims = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getSexe(): ?string {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self {
        $this->sexe = $sexe;

        return $this;
    }

    public function getContact(): ?string {
        return $this->contact;
    }

    public function setContact(?string $contact): self {
        $this->contact = $contact;

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

    public function getDatenaiss(): ?DateTimeInterface {
        return $this->datenaiss;
    }

    public function setDatenaiss(?DateTimeInterface $datenaiss): self {
          $this->datenaiss = $datenaiss;

        return $this;
    }

    public function getLieunaiss(): ?string {
        return $this->lieunaiss;
    }

    public function setLieunaiss(?string $lieunaiss): self {
        $this->lieunaiss = $lieunaiss;

        return $this;
    }

    public function getNumpiece(): ?string {
        return $this->numpiece;
    }

    public function setNumpiece(?string $numpiece): self {
        $this->numpiece = $numpiece;

        return $this;
    }

    public function getComptefacebook(): ?string {
        return $this->comptefacebook;
    }

    public function setComptefacebook(?string $comptefacebook): self {
        $this->comptefacebook = $comptefacebook;

        return $this;
    }

    public function getNiveauetude(): ?string {
        return $this->niveauetude;
    }

    public function setNiveauetude(?string $niveauetude): self {
        $this->niveauetude = $niveauetude;

        return $this;
    }

    public function getClassenfant(): ?string {
        return $this->classenfant;
    }

    public function setClassenfant(?string $classenfant): self {
        $this->classenfant = $classenfant;

        return $this;
    }

    public function getGroupesang(): ?string {
        return $this->groupesang;
    }

    public function setGroupesang(?string $groupesang): self {
        $this->groupesang = $groupesang;

        return $this;
    }

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self {
        $this->photo = $photo;

        return $this;
    }

  

    public function getEthnie(): ?Ethnie {
        return $this->ethnie;
    }

    public function setEthnie(?Ethnie $ethnie): self {
        $this->ethnie = $ethnie;

        return $this;
    }

   

    public function getCreateAt(): ?DateTimeInterface {
        return $this->createAt;
    }

    public function setCreateAt(?DateTimeInterface $createAt): self {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?DateTimeInterface {
        return $this->updateAt;
    }

    public function setUpdateAt(?DateTimeInterface $updateAt): self {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getDeleteAt(): ?DateTimeInterface {
        return $this->deleteAt;
    }

    public function setDeleteAt(?DateTimeInterface $deleteAt): self {
        $this->deleteAt = $deleteAt;

        return $this;
    }

    /**
     * @return Collection|Inscrire[]
     */
    public function getInscrires(): Collection {
        return $this->inscrires;
    }

    public function addInscrire(Inscrire $inscrire): self {
        if (!$this->inscrires->contains($inscrire)) {
            $this->inscrires[] = $inscrire;
            $inscrire->setEnfant($this);
        }

        return $this;
    }

    public function removeInscrire(Inscrire $inscrire): self {
        if ($this->inscrires->removeElement($inscrire)) {
            // set the owning side to null (unless already changed)
            if ($inscrire->getEnfant() === $this) {
                $inscrire->setEnfant(null);
            }
        }

        return $this;
    }

    public function getEtatenfant(): ?bool {
        return $this->etatenfant;
    }

    public function setEtatenfant(?bool $etatenfant): self {
        $this->etatenfant = $etatenfant;

        return $this;
    }

    public function getPhotoFile(): ?string {
        return $this->photoFile;
    }

    public function setPhotoFile(?string $photoFile): self {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getEglise(): ?Eglise {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self {
        $this->eglise = $eglise;

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

    public function getFamille(): ?Famille {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self {
        $this->famille = $famille;

        return $this;
    }



    public function __toString() {
        return $this->getNom();
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

//    /**
//     * @return Collection|Parentenfant[]
//     */
//    public function getParentenfants(): Collection
//    {
//        return $this->parentenfants;
//    }
//
//    public function addParentenfant(Parentenfant $parentenfant): self
//    {
//        if (!$this->parentenfants->contains($parentenfant)) {
//            $this->parentenfants[] = $parentenfant;
//            $parentenfant->setEnfant($this);
//        }
//
//        return $this;
//    }
//
//    public function removeParentenfant(Parentenfant $parentenfant): self
//    {
//        if ($this->parentenfants->removeElement($parentenfant)) {
//            // set the owning side to null (unless already changed)
//            if ($parentenfant->getEnfant() === $this) {
//                $parentenfant->setEnfant(null);
//            }
//        }
//
//        return $this;
//    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getPeremembre(): ?Fidele
    {
        return $this->peremembre;
    }

    public function setPeremembre(?Fidele $peremembre): self
    {
        $this->peremembre = $peremembre;

        return $this;
    }

    public function getMerembre(): ?Fidele
    {
        return $this->merembre;
    }

    public function setMerembre(?Fidele $merembre): self
    {
        $this->merembre = $merembre;

        return $this;
    }

    public function getPere(): ?string
    {
        return $this->pere;
    }

    public function setPere(?string $pere): self
    {
        $this->pere = $pere;

        return $this;
    }

    public function getMere(): ?string
    {
        return $this->mere;
    }

    public function setMere(?string $mere): self
    {
        $this->mere = $mere;

        return $this;
    }

   

    public function getLieuvivre(): ?string
    {
        return $this->lieuvivre;
    }

    public function setLieuvivre(?string $lieuvivre): self
    {
        $this->lieuvivre = $lieuvivre;

        return $this;
    }

    public function getHandicap(): ?string
    {
        return $this->handicap;
    }

    public function setHandicap(?string $handicap): self
    {
        $this->handicap = $handicap;

        return $this;
    }

    public function getVieparent(): ?string
    {
        return $this->vieparent;
    }

    public function setVieparent(?string $vieparent): self
    {
        $this->vieparent = $vieparent;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(?string $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    public function getSituationparent(): ?string
    {
        return $this->situationparent;
    }

    public function setSituationparent(?string $situationparent): self
    {
        $this->situationparent = $situationparent;

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

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getMaladie(): ?string
    {
        return $this->maladie;
    }

    public function setMaladie(?string $maladie): self
    {
        $this->maladie = $maladie;

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
            $detailenfantactivite->setEnfant($this);
        }

        return $this;
    }

    public function removeDetailenfantactivite(Detailenfantactivite $detailenfantactivite): self
    {
        if ($this->detailenfantactivites->removeElement($detailenfantactivite)) {
            // set the owning side to null (unless already changed)
            if ($detailenfantactivite->getEnfant() === $this) {
                $detailenfantactivite->setEnfant(null);
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
            $presenceculteecodim->setEnfant($this);
        }

        return $this;
    }

    public function removePresenceculteecodim(Presenceculteecodim $presenceculteecodim): self
    {
        if ($this->presenceculteecodims->removeElement($presenceculteecodim)) {
            // set the owning side to null (unless already changed)
            if ($presenceculteecodim->getEnfant() === $this) {
                $presenceculteecodim->setEnfant(null);
            }
        }

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

    
 



}
