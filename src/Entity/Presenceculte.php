<?php

namespace App\Entity;

use App\Repository\PresenceculteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PresenceculteRepository::class)
 * @ORM\Table(
 *     name="presenceculte",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="presence_unique",
 *             columns={"fidele_id", "culte_id"}
 *         )
 *     }
 * )
 */
class Presenceculte extends AbstractEntity 
{ 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="presences")
     * @ORM\JoinColumn(name="fidele_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Culte::class, inversedBy="presences")
     * @ORM\JoinColumn(name="culte_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $culte;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
     * @ORM\JoinColumn(name="eglise_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
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

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCulte(): ?Culte
    {
        return $this->culte;
    }

    public function setCulte(?Culte $culte): self
    {
        $this->culte = $culte;
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
}