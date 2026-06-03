<?php

namespace App\Entity;

use App\Repository\PastoraleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PastoraleRepository::class)
 */
class Pastorale  extends AbstractEntity
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
    private $datepastorale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datefin;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="pastorales")
     */
    private $pasteur1;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="pastorales")
     */
    private $pasteur2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="pastorales")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Presencepastorale::class, mappedBy="pastorale")
     */
    private $presencepastorales;
    
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $ordredujour;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pv;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brochureFilename;
    

    public function __construct()
    {
        $this->presencepastorales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatepastorale(): ?\DateTimeInterface
    {
        return $this->datepastorale;
    }

    public function setDatepastorale(?\DateTimeInterface $datepastorale): self
    {
        $this->datepastorale = $datepastorale;

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

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getPasteur1(): ?Fidele
    {
        return $this->pasteur1;
    }

    public function setPasteur1(?Fidele $pasteur1): self
    {
        $this->pasteur1 = $pasteur1;

        return $this;
    }

    public function getPasteur2(): ?Fidele
    {
        return $this->pasteur2;
    }

    public function setPasteur2(?Fidele $pasteur2): self
    {
        $this->pasteur2 = $pasteur2;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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
            $presencepastorale->setPastorale($this);
        }

        return $this;
    }

    public function removePresencepastorale(Presencepastorale $presencepastorale): self
    {
        if ($this->presencepastorales->removeElement($presencepastorale)) {
            // set the owning side to null (unless already changed)
            if ($presencepastorale->getPastorale() === $this) {
                $presencepastorale->setPastorale(null);
            }
        }

        return $this;
    }
    
        public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getOrdredujour(): ?string
    {
        return $this->ordredujour;
    }

    public function setOrdredujour(?string $ordredujour): self
    {
        $this->ordredujour = $ordredujour;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPv(): ?string
    {
        return $this->pv;
    }

    public function setPv(?string $pv): self
    {
        $this->pv = $pv;

        return $this;
    }

    public function getBrochureFilename(): ?string
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename(?string $brochureFilename): self
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }
}
