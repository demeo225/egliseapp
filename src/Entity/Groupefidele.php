<?php

namespace App\Entity;

use App\Repository\GroupefideleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GroupefideleRepository::class)
 */
class Groupefidele extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatgroupe;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $rolegroupe;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="groupefideles")
     *  @Assert\NotBlank()
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="groupefideles")
     *  @Assert\NotBlank()
     */
    private $groupe;

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
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="groupefidele")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="groupefideles")
     */
    private $departement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatgroupe(): ?bool
    {
        return $this->etatgroupe;
    }

    public function setEtatgroupe(?bool $etatgroupe): self
    {
        $this->etatgroupe = $etatgroupe;

        return $this;
    }

    public function getRolegroupe(): ?string
    {
        return $this->rolegroupe;
    }

    public function setRolegroupe(?string $rolegroupe): self
    {
        $this->rolegroupe = $rolegroupe;

        return $this;
    }

    public function getFidele(): ?Fidele
    {
        return $this->fidele;
    }

    public function setFidele(?Fidele $fidele): self
    {
        $this->fidele = $fidele;

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

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

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
}
