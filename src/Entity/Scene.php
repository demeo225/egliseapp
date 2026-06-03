<?php

namespace App\Entity;

use App\Repository\SceneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SceneRepository::class)
 */
class Scene extends AbstractEntity
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
    private $datescene;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detail;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="scenes")
     */
    private $pasteur1;

    /**
     * @ORM\ManyToOne(targetEntity=Fidele::class, inversedBy="scenes")
     */
    private $pasteur2;

    /**
     * @ORM\ManyToOne(targetEntity=Eglise::class, inversedBy="scenes")
     */
    private $eglise;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatescene(): ?\DateTimeInterface
    {
        return $this->datescene;
    }

    public function setDatescene(?\DateTimeInterface $datescene): self
    {
        $this->datescene = $datescene;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

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

    public function getEglise(): ?Eglise
    {
        return $this->eglise;
    }

    public function setEglise(?Eglise $eglise): self
    {
        $this->eglise = $eglise;

        return $this;
    }
}
