<?php

namespace App\Entity;

use App\Repository\CulteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CulteRepository::class)
 */
class Culte extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateculte;

    

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $orateur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $typemessager;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nmbrehomme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nobrefemme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrefant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalfidele;

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
     * @ORM\Column(type="string", length=128, nullable=true))
     */
    private $categorieculte;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="culte")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="cultes")
     */
    private $messager;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="dirigeant")
     */
    private $dirigeant;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $invite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ame;

    /**
     * @ORM\OneToMany(targetEntity=Invite::class, mappedBy="culte")
     */
    private $invites;

    /**
     * @ORM\ManyToOne(targetEntity=Typeculte::class, inversedBy="cultes")
     */
    private $typeculte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $tokenPresence = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $dateExpirationQr = null;

    public function __construct()
    {
        $this->invites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateculte(): ?\DateTimeInterface
    {
        return $this->dateculte;
    }

    public function setDateculte(?\DateTimeInterface $dateculte): self
    {
        $this->dateculte = $dateculte;

        return $this;
    }


    public function getOrateur(): ?string
    {
        return $this->orateur;
    }

    public function setOrateur(?string $orateur): self
    {
        $this->orateur = $orateur;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getTypemessager(): ?bool
    {
        return $this->typemessager;
    }

    public function setTypemessager(?bool $typemessager): self
    {
        $this->typemessager = $typemessager;

        return $this;
    }

    public function getNmbrehomme(): ?int
    {
        return $this->nmbrehomme;
    }

    public function setNmbrehomme(?int $nmbrehomme): self
    {
        $this->nmbrehomme = $nmbrehomme;

        return $this;
    }

    public function getNobrefemme(): ?int
    {
        return $this->nobrefemme;
    }

    public function setNobrefemme(?int $nobrefemme): self
    {
        $this->nobrefemme = $nobrefemme;

        return $this;
    }

    public function getNbrefant(): ?int
    {
        return $this->nbrefant;
    }

    public function setNbrefant(?int $nbrefant): self
    {
        $this->nbrefant = $nbrefant;

        return $this;
    }

    public function getTotalfidele(): ?int
    {
        return $this->totalfidele;
    }

    public function setTotalfidele(?int $totalfidele): self
    {
        $this->totalfidele = $totalfidele;

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

 

    public function getCategorieculte(): ?string
    {
        return $this->categorieculte;
    }

    public function setCategorieculte(string $categorieculte): self
    {
        $this->categorieculte = $categorieculte;

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

    public function getMessager(): ?Fidele
    {
        return $this->messager;
    }

    public function setMessager(?Fidele $messager): self
    {
        $this->messager = $messager;

        return $this;
    }

    public function getDirigeant(): ?Fidele
    {
        return $this->dirigeant;
    }

    public function setDirigeant(?Fidele $dirigeant): self
    {
        $this->dirigeant = $dirigeant;

        return $this;
    }

    public function getInvite(): ?int
    {
        return $this->invite;
    }

    public function setInvite(?int $invite): self
    {
        $this->invite = $invite;

        return $this;
    }

    public function getAme(): ?int
    {
        return $this->ame;
    }

    public function setAme(?int $ame): self
    {
        $this->ame = $ame;

        return $this;
    }

    /**
     * @return Collection<int, Invite>
     */
    public function getInvites(): Collection
    {
        return $this->invites;
    }

    public function addInvite(Invite $invite): self
    {
        if (!$this->invites->contains($invite)) {
            $this->invites[] = $invite;
            $invite->setCulte($this);
        }

        return $this;
    }

    public function removeInvite(Invite $invite): self
    {
        if ($this->invites->removeElement($invite)) {
            // set the owning side to null (unless already changed)
            if ($invite->getCulte() === $this) {
                $invite->setCulte(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->dateculte;
    }

    public function getTypeculte(): ?Typeculte
    {
        return $this->typeculte;
    }

    public function setTypeculte(?Typeculte $typeculte): self
    {
        $this->typeculte = $typeculte;

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

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
        public function getTokenPresence(): ?string
        {
            return $this->tokenPresence;
        }

        public function setTokenPresence(?string $tokenPresence): self
        {
            $this->tokenPresence = $tokenPresence;
            return $this;
        }

        public function getDateExpirationQr(): ?\DateTimeInterface
        {
            return $this->dateExpirationQr;
        }

        public function setDateExpirationQr(?\DateTimeInterface $dateExpirationQr): self
        {
            $this->dateExpirationQr = $dateExpirationQr;
            return $this;
        }
}
