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

        /**
         * @ORM\OneToMany(targetEntity=Presenceculte::class, mappedBy="culte", cascade={"persist", "remove"})
         */

    private $presencecultes;
    private ?\DateTimeInterface $dateExpirationQr = null;

    public function __construct()
    {
        $this->invites = new ArrayCollection();
            $this->presencecultes = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
 /**
     * Vérifie si le token a expiré (24h)
     */
    public function isTokenExpired(): bool
    {
        if (!$this->tokenPresence || !$this->dateExpirationQr) {
            return true; // Pas de token ou pas de date d'expiration = considéré comme expiré
        }

        $now = new \DateTime();
        return $now > $this->dateExpirationQr;
    }

    /**
     * Vérifie si le token est valide (non expiré)
     */
    public function isValidToken(): bool
    {
        return !$this->isTokenExpired();
    }

    /**
     * Génère un nouveau token avec expiration à 24h
     */
    public function generateToken(): void
    {
        $this->tokenPresence = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->dateExpirationQr = (new \DateTime())->modify('+24 hours');
    }

    /**
     * Renouvelle le token (nouveau token + nouvelle date d'expiration)
     */
    public function renewToken(): void
    {
        $this->generateToken();
    }

    /**
     * Rafraîchit le token si expiré (pour les utilisations prolongées)
     */
    public function refreshTokenIfExpired(): bool
    {
        if ($this->isTokenExpired() && $this->tokenPresence) {
            $this->renewToken();
            return true;
        }
        return false;
    }

    /**
     * Vérifie si le token est encore valide pour un nombre d'heures donné
     */
    public function isTokenValidForHours(int $hours): bool
    {
        if (!$this->dateExpirationQr) {
            return false;
        }

        $now = new \DateTime();
        $expiration = clone $this->dateExpirationQr;
        $limit = clone $now;
        $limit->modify("+{$hours} hours");

        return $expiration > $limit;
    }

    /**
     * Obtient le temps restant avant expiration en heures
     */
    public function getTokenRemainingHours(): float
    {
        if (!$this->dateExpirationQr) {
            return 0;
        }

        $now = new \DateTime();
        $diff = $now->diff($this->dateExpirationQr);
        
        if ($now > $this->dateExpirationQr) {
            return 0;
        }

        return round($diff->h + ($diff->i / 60) + ($diff->d * 24), 1);
    }

    /**
     * Obtient le temps restant avant expiration en format lisible
     */
    public function getTokenRemainingTime(): string
    {
        if (!$this->dateExpirationQr) {
            return 'Expiré';
        }

        $now = new \DateTime();
        if ($now > $this->dateExpirationQr) {
            return 'Expiré';
        }

        $diff = $now->diff($this->dateExpirationQr);
        
        if ($diff->d > 0) {
            return $diff->d . 'j ' . $diff->h . 'h';
        }
        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'min';
        }
        if ($diff->i > 0) {
            return $diff->i . 'min ' . $diff->s . 's';
        }
        return 'Moins d\'une minute';
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

        
/**
 * @return Collection<int, Presenceculte>
 */
public function getPresencecultes(): Collection
{
    return $this->presencecultes;
}

public function addPresence(Presenceculte $presence): self
{
    if (!$this->presencecultes->contains($presence)) {
        $this->presencecultes[] = $presence;
        $presence->setCulte($this);
    }
    return $this;
}

public function removePresence(Presenceculte $presence): self
{
    if ($this->presencecultes->removeElement($presence)) {
        if ($presence->getCulte() === $this) {
            $presence->setCulte(null);
        }
    }
    return $this;
}


}
