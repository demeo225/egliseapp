<?php

namespace App\Entity;

use App\Repository\EvangelisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=EvangelisationRepository::class)
 */
class Evangelisation extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("public")
     */
    private $dateop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     */
    private $lieu;
    
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     * 
     */
    private $createAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     * [Assert\Regex(
        pattern: '/^[a-z]+$/i',
        htmlPattern: '^[a-zA-Z]+$'
    )
     */
    private $responsable1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("public")
     * [Assert\Regex(
        pattern: '/^[a-z]+$/i',
        htmlPattern: '^[a-zA-Z]+$'
    )
     */
    private $responsable2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("public")
     */
    private $personnes1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("public")
     */
    private $personnes2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("public")
     */
    private $personnes3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("public")
     */
    private $personnes4;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="evangelisations")
     */
    private $eglise;

    /**
     * @ORM\OneToMany(targetEntity=Ame::class, mappedBy="evangelisation")
     */
    private $ames;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observation;

    public function __construct()
    {
        $this->ames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateop(): ?\DateTimeInterface
    {
        return $this->dateop;
    }

    public function setDateop(?\DateTimeInterface $dateop): self
    {
        $this->dateop = $dateop;

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

    public function getResponsable1(): ?string
    {
        return $this->responsable1;
    }

    public function setResponsable1(?string $responsable1): self
    {
        $this->responsable1 = $responsable1;

        return $this;
    }

    public function getResponsable2(): ?string
    {
        return $this->responsable2;
    }

    public function setResponsable2(?string $responsable2): self
    {
        $this->responsable2 = $responsable2;

        return $this;
    }

    public function getPersonnes1(): ?int
    {
        return $this->personnes1;
    }

    public function setPersonnes1(?int $personnes1): self
    {
        $this->personnes1 = $personnes1;

        return $this;
    }

    public function getPersonnes2(): ?int
    {
        return $this->personnes2;
    }

    public function setPersonnes2(?int $personnes2): self
    {
        $this->personnes2 = $personnes2;

        return $this;
    }

    public function getPersonnes3(): ?int
    {
        return $this->personnes3;
    }

    public function setPersonnes3(?int $personnes3): self
    {
        $this->personnes3 = $personnes3;

        return $this;
    }

    public function getPersonnes4(): ?int
    {
        return $this->personnes4;
    }

    public function setPersonnes4(?int $personnes4): self
    {
        $this->personnes4 = $personnes4;

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
            $ame->setEvangelisation($this);
        }

        return $this;
    }

    public function removeAme(Ame $ame): self
    {
        if ($this->ames->removeElement($ame)) {
            // set the owning side to null (unless already changed)
            if ($ame->getEvangelisation() === $this) {
                $ame->setEvangelisation(null);
            }
        }

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }
    
    public function __toString() {
        return $this->dateop->format('d-m-Y');
    }
}
