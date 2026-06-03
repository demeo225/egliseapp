<?php

namespace App\Entity;

use App\Repository\MariageRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MariageRepository::class)
 */
class Mariage extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datemariage;

        /**
     * @ORM\Column(type="string", length=48, nullable=true)
     * 
     */
    private $typeregime;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieumariage;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $pasteurmariage;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $epouse;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $epoux;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $temoinepoux;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $temoinepouse;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $parrain;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $photoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $naturedon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $especedon;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatmariage;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="mariage")
     */
    private $eglise;

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
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $regime;



    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="epouxmembre", cascade={"persist"})
     * 
     */
    private $epouxmembre;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="epousemembre", cascade={"persist"})
     */
    private $epousemembre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatemariage(): ?\DateTimeInterface
    {
        return $this->datemariage;
    }

    public function setDatemariage(?\DateTimeInterface $datemariage): self
    {
        $this->datemariage = $datemariage;

        return $this;
    }

    public function getLieumariage(): ?string
    {
        return $this->lieumariage;
    }

    public function setLieumariage(?string $lieumariage): self
    {
        $this->lieumariage = $lieumariage;

        return $this;
    }

    public function getPasteurmariage(): ?string
    {
        return $this->pasteurmariage;
    }

    public function setPasteurmariage(?string $pasteurmariage): self
    {
        $this->pasteurmariage = $pasteurmariage;

        return $this;
    }

    public function getEpouse(): ?string
    {
        return $this->epouse;
    }

    public function setEpouse(?string $epouse): self
    {
        $this->epouse = $epouse;

        return $this;
    }

    public function getEpoux(): ?string
    {
        return $this->epoux;
    }

    public function setEpoux(?string $epoux): self
    {
        $this->epoux = $epoux;

        return $this;
    }

    public function getTemoinepoux(): ?string
    {
        return $this->temoinepoux;
    }

    public function setTemoinepoux(?string $temoinepoux): self
    {
        $this->temoinepoux = $temoinepoux;

        return $this;
    }

    public function getTemoinepouse(): ?string
    {
        return $this->temoinepouse;
    }

    public function setTemoinepouse(?string $temoinepouse): self
    {
        $this->temoinepouse = $temoinepouse;

        return $this;
    }

    public function getParrain(): ?string
    {
        return $this->parrain;
    }

    public function setParrain(?string $parrain): self
    {
        $this->parrain = $parrain;

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

    public function getPhotoFile(): ?string
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?string $photoFile): self
    {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getNaturedon(): ?string
    {
        return $this->naturedon;
    }

    public function setNaturedon(?string $naturedon): self
    {
        $this->naturedon = $naturedon;

        return $this;
    }

    public function getEspecedon(): ?int
    {
        return $this->especedon;
    }

    public function setEspecedon(?int $especedon): self
    {
        $this->especedon = $especedon;

        return $this;
    }

    public function getEtatmariage(): ?bool
    {
        return $this->etatmariage;
    }

    public function setEtatmariage(?bool $etatmariage): self
    {
        $this->etatmariage = $etatmariage;

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

    public function getRegime(): ?string
    {
        return $this->regime;
    }

    public function setRegime(?string $regime): self
    {
        $this->regime = $regime;

        return $this;
    }

    public function getEpouxmembre(): ?Fidele
    {
        return $this->epouxmembre;
    }

    public function setEpouxmembre(?Fidele $epouxmembre): self
    {
        $this->epouxmembre = $epouxmembre;

        return $this;
    }

    public function getEpousemembre(): ?Fidele
    {
        return $this->epousemembre;
    }

    public function setEpousemembre(?Fidele $epousemembre): self
    {
        $this->epousemembre = $epousemembre;

        return $this;
    }
    
        public function getTyperegime(): ?string {
        return $this->typeregime;
    }

    public function setTyperegime(string $typeregime): self {
        $this->typeregime = $typeregime;

        return $this;
    }
}
