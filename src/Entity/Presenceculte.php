<?php

namespace App\Entity;

use App\Repository\PresenceculteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PresenceculteRepository::class)
 */
/**
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="presence_unique",
 *             columns={"fidele_id","culte_id"}
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
     * @ORM\ManyToOne(targetEntity=Fidele::class)
     */
    private $fidele;

    /**
     * @ORM\ManyToOne(targetEntity=Culte::class)
     */
    private $culte;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class)
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
}
