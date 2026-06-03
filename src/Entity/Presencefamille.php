<?php

namespace App\Entity;

use App\Repository\PresencefamilleRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PresencefamilleRepository::class)
 */
class Presencefamille extends AbstractEntity 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="presencefamilles")
     */
    private $famille;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="presencefamilles")
     */
    private $eglise;

    /**
     * @ORM\ManyToOne(targetEntity=Seancefamille::class, inversedBy="presencefamilles")
     */
    private $seancefamille;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="presencefamilles")
     */
    private $fidele;
      
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

    public function getFamille(): ?Famille
    {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;

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

    public function getSeancefamille(): ?Seancefamille
    {
        return $this->seancefamille;
    }

    public function setSeancefamille(?Seancefamille $seancefamille): self
    {
        $this->seancefamille = $seancefamille;

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
