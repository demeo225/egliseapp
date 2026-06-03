<?php

namespace App\Entity;

use App\Repository\CultecodimRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CultecodimRepository::class)
 */
class Cultecodim extends AbstractEntity
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrefille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbregarcon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moniteur1;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $moniteur2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=Classecodim::class, inversedBy="cultecodims")
     */
    private $classecodim;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $versets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $versetretenir;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $offrande;

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
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="cultecodim")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Presenceculteecodim::class, mappedBy="cultecodim")
     */
    private $presenceculteecodims;

    public function __construct()
    {
        $this->presenceculteecodims = new ArrayCollection();
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

    public function getNbrefille(): ?int
    {
        return $this->nbrefille;
    }

    public function setNbrefille(?int $nbrefille): self
    {
        $this->nbrefille = $nbrefille;

        return $this;
    }

    public function getNbregarcon(): ?int
    {
        return $this->nbregarcon;
    }

    public function setNbregarcon(?int $nbregarcon): self
    {
        $this->nbregarcon = $nbregarcon;

        return $this;
    }

    public function getMoniteur1(): ?string
    {
        return $this->moniteur1;
    }

    public function setMoniteur1(?string $moniteur1): self
    {
        $this->moniteur1 = $moniteur1;

        return $this;
    }

    public function getMoniteur2(): ?string
    {
        return $this->moniteur2;
    }

    public function setMoniteur2(?string $moniteur2): self
    {
        $this->moniteur2 = $moniteur2;

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

    public function getClassecodim(): ?Classecodim
    {
        return $this->classecodim;
    }

    public function setClassecodim(?Classecodim $classecodim): self
    {
        $this->classecodim = $classecodim;

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

    public function getVersetretenir(): ?string
    {
        return $this->versetretenir;
    }

    public function setVersetretenir(?string $versetretenir): self
    {
        $this->versetretenir = $versetretenir;

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
            $presenceculteecodim->setCultecodim($this);
        }

        return $this;
    }

    public function removePresenceculteecodim(Presenceculteecodim $presenceculteecodim): self
    {
        if ($this->presenceculteecodims->removeElement($presenceculteecodim)) {
            // set the owning side to null (unless already changed)
            if ($presenceculteecodim->getCultecodim() === $this) {
                $presenceculteecodim->setCultecodim(null);
            }
        }

        return $this;
    }
    
    public function __toString() {
        return $this->dateculte->format('d-m-Y');
    }
}
