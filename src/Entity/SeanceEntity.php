<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;




/**
 * @ORM\MappedSuperclass
 *
 * @author Lynova Tech
 */
class SeanceEntity extends AbstractEntity {
    
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
     * @ORM\Column(type="date", nullable=true)
     */
    private $datesuper;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;
    
       /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
        private $typeofficiant;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrepresent;

      /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $femme;
    
      /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $enfant;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objet;  
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $versets;

    /**
     * @ORM\Column(type="text",  nullable=true)
     */
    private $resume;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $offrande;

    /**
     * @ORM\Column(type="time",  nullable=true)
     */
    private $heuredebut;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heurefin;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $typeactivite;
   

    public function getDatesuper(): ?DateTimeInterface
    {
        return $this->datesuper;
    }

    public function setDatesuper(?DateTimeInterface $datesuper): self
    {
        $this->datesuper = $datesuper;

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

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

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


    public function getNbrepresent(): ?int
    {
        return $this->nbrepresent;
    }

    public function setNbrepresent(?int $nbrepresent): self
    {
        $this->nbrepresent = $nbrepresent;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(?string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getVersets(): ?string
    {
        return $this->versets;
    }

    public function setVersets(?string $versets): self
    {
        $this->versets = $versets;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getOffrande(): ?int
    {
        return $this->offrande;
    }

    public function setOffrande(?int $offrande): self
    {
        $this->offrande = $offrande;

        return $this;
    }

    public function getHeuredebut(): ?DateTimeInterface
    {
        return $this->heuredebut;
    }

    public function setHeuredebut(?DateTimeInterface $heuredebut): self
    {
        $this->heuredebut = $heuredebut;

        return $this;
    }

    public function getHeurefin(): ?DateTimeInterface
    {
        return $this->heurefin;
    }

    public function setHeurefin(?DateTimeInterface $heurefin): self
    {
        $this->heurefin = $heurefin;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

        public function getTypeactivite(): ?string
    {
        return $this->typeactivite;
    }

    public function setTypeactivite(?string $typeactivite): self
    {
        $this->typeactivite = $typeactivite;

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

      public function getTypeofficiant(): ?string
    {
        return $this->typeofficiant;
    }

    public function setTypeofficiant(string $typeofficiant): self
    {
        $this->typeofficiant = $typeofficiant;

        return $this;
    }
    
    
      public function getFemme(): ?int
    {
        return $this->femme;
    }

    public function setFemme(?int $femme): self
    {
        $this->femme = $femme;

        return $this;
    }
    
    
    
        public function getEnfant(): ?int
    {
        return $this->enfant;
    }

    public function setEnfant(?int $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }
}
