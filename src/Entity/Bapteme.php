<?php

namespace App\Entity;

use App\Repository\BaptemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BaptemeRepository::class)
 */
class Bapteme extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pasteurofficient;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $promotion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datebapteme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieubapteme;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $femme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $homme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=Fidele::class, mappedBy="bapteme", cascade ={"persist"})
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="bapteme")
     */
    private $eglise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parrain;

    public function __construct() {
        $this->fidele = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getPasteurofficient(): ?string {
        return $this->pasteurofficient;
    }

    public function setPasteurofficient(?string $pasteurofficient): self {
        $this->pasteurofficient = $pasteurofficient;

        return $this;
    }

    public function getPromotion(): ?string {
        return $this->promotion;
    }

    public function setPromotion(string $promotion): self {
        $this->promotion = $promotion;

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

    public function getFemme(): ?int {
        return $this->femme;
    }

    public function setFemme(?int $femme): self {
        $this->femme = $femme;

        return $this;
    }

    public function getHomme(): ?int {
        return $this->homme;
    }

    public function setHomme(?int $homme): self {
        $this->homme = $homme;

        return $this;
    }

    public function getTotal(): ?int {
        return $this->total;
    }

    public function setTotal(?int $total): self {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|Fidele[]
     */
    public function getFidele(): Collection {
        return $this->fidele;
    }

    public function addFidele(Fidele $fidele): self {
        if (!$this->fidele->contains($fidele)) {
            $this->fidele[] = $fidele;
            $fidele->setBapteme($this);
        }

        return $this;
    }

    public function removeFidele(Fidele $fidele): self {
        if ($this->fidele->removeElement($fidele)) {
            // set the owning side to null (unless already changed)
            if ($fidele->getBapteme() === $this) {
                $fidele->setBapteme(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->promotion;
        ;
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

    public function getParrain(): ?string
    {
        return $this->parrain;
    }

    public function setParrain(?string $parrain): self
    {
        $this->parrain = $parrain;

        return $this;
    }

}
