<?php

namespace App\Entity;

use App\Repository\PatrimoineRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PatrimoineRepository::class)
 */
class Patrimoine extends AbstractEntity {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $article;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prixunitaire;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prixtotal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valeuregl;

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
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="patrimoine")
     */
    private $eglise;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datepatrimoine;

    public function getId(): ?int {
        return $this->id;
    }

    public function getArticle(): ?string {
        return $this->article;
    }

    public function setArticle(?string $article): self {
        $this->article = $article;

        return $this;
    }

    public function getQuantite(): ?int {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixunitaire(): ?int {
        return $this->prixunitaire;
    }

    public function setPrixunitaire(?int $prixunitaire): self {
        $this->prixunitaire = $prixunitaire;

        return $this;
    }

    public function getPrixtotal(): ?int {
        return $this->prixtotal;
    }

    public function setPrixtotal(?int $prixtotal): self {
        $this->prixtotal = $prixtotal;

        return $this;
    }

    public function getValeuregl(): ?int {
        return $this->valeuregl;
    }

    public function setValeuregl(?int $valeuregl): self {
        $this->valeuregl = $valeuregl;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }

    public function getDatepatrimoine(): ?\DateTimeInterface
    {
        return $this->datepatrimoine;
    }

    public function setDatepatrimoine(?\DateTimeInterface $datepatrimoine): self
    {
        $this->datepatrimoine = $datepatrimoine;

        return $this;
    }

}
