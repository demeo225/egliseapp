<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="Ce mail a déjà été utilisé")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface {
 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

//    /**
//     * @ORM\Column(type="json")
//     */
//    private $roles = [];

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomuser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoFile;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginAt;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="user")
     */
    private $eglise;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Zone::class, inversedBy="users")
     */
    private $zone;

    /**
     * @ORM\ManyToOne(targetEntity=Cellule::class, inversedBy="users")
     */
    private $cellule;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="users")
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="users")
     */
    private $departement;

    /**
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="users")
     */
    private $famille;

    public function __construct() {
        $this->etat = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
       // $this->zone = new ArrayCollection();
       // $this->famille = new ArrayCollection();
        //$this->cellule = new ArrayCollection();
       // $this->departement = new ArrayCollection();
      //  $this->groupe = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
//    public function getRoles(): array {
//        $roles = $this->roles;
//        // guarantee every user at least has ROLE_USER
////        $roles[] = 'ROLE_USER';
//         
//
//        return array_unique($roles);
//    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    function removeRole($role) {
        $this->roles[] = $role;
    }

    function addRole($role) {
        $this->roles[] = $role;
    }

    /** @see \Serializable::serialize() */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->etat,
                // see section on salt below
                // $this->salt,
        ));
    }
     /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list (
                $this->id,
                $this->email,
                $this->password,
                $this->etat,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNomuser(): ?string {
        return $this->nomuser;
    }

    public function setNomuser(?string $nomuser): self {
        $this->nomuser = $nomuser;

        return $this;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPhotoFile(): ?string {
        return $this->photoFile;
    }

    public function setPhotoFile(?string $photoFile): self {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getEtat(): ?bool {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self {
        $this->etat = $etat;

        return $this;
    }

    public function getPlainPassword() {
        return $this->plainPassword;
    }

    public function setPlainPassword($password) {
        $this->plainPassword = $password;
    }

    public function getLastLoginAt(): ?\DateTimeInterface {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self {
        $this->lastLoginAt = $lastLoginAt;

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

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self {
        $this->photo = $photo;

        return $this;
    }

    public function getEglise(): ?Eglise {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self {
        $this->eglise = $eglise;

        return $this;
    }
     

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;  
    }
    
     public function isExpired(): bool
    {
      //  $this->getEglise()->getDeleted2At();
        return $this->eglise->getEtat() == 0;  
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

     public function getCellule(): ?Cellule
     {
         return $this->cellule;
     }

     public function setCellule(?Cellule $cellule): self
     {
         $this->cellule = $cellule;

         return $this;
     }

     public function getGroupe(): ?Groupe
     {
         return $this->groupe;
     }

     public function setGroupe(?Groupe $groupe): self
     {
         $this->groupe = $groupe;

         return $this;
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

     public function getFamille(): ?Famille
     {
         return $this->famille;
     }

     public function setFamille(?Famille $famille): self
     {
         $this->famille = $famille;

         return $this;
     }
}
