<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Cotiser 
{
     /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     * @JMS\SerializedName("createdBy")
     * @var User
     */
    protected $createdBy;

    /**
     * @var string $createdFromIp
     * Assert\Ip
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(length=45, nullable=true)
     * @JMS\SerializedName("createdFromIp")
     */
    protected $createdFromIp;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     * @JMS\SerializedName("updatedBy")
     * @var User
     */
    protected $updatedBy;

    /**
     * @var string $updatedFromIp
     *
     * @Gedmo\IpTraceable(on="update")
     * @ORM\Column(length=45, nullable=true)
     * @JMS\SerializedName("updatedFromIp")
     */
    protected $updatedFromIp;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     * @JMS\SerializedName("deletedAt")
     * @JMS\Type("DateTime<'d-m-Y H:i:s'>") 
     * @var DateTime Date de suppression
     */
    protected $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     * @JMS\SerializedName("deletedBy")
     * @var User
     */
    protected $deletedBy;

    /**
     * @var string $updatedFromIp
     *
     * @ORM\Column(length=45, nullable=true)
     * @JMS\SerializedName("deletedFromIp")
     */
    protected $deletedFromIp;

    /** @ORM\Column(type="boolean") */
    protected $editable = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datecotiser;

     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datefin;
    
        /**
     * @ORM\Column(type="string", length = 128, nullable=true)
     */
    private $objet;
    
     /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;
   
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $etatcotiser;

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

    public function getDatecotiser(): ?DateTimeInterface
    {
        return $this->datecotiser;
    }

    public function setDatecotiser(?DateTimeInterface $datecotiser): self
    {
        $this->datecotiser = $datecotiser;

        return $this;
    }
    
       public function getDatefin(): ?DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

 
    public function getEtatcotiser(): ?bool
    {
        return $this->etatcotiser;
    }

    public function setEtatcotiser(?bool $etatcotiser): self
    {
        $this->etatcotiser = $etatcotiser;

        return $this;
    }

    public function getCotisation(): ?Cotisation
    {
        return $this->cotisation;
    }

    public function setCotisation(?Cotisation $cotisation): self
    {
        $this->cotisation = $cotisation;

        return $this;
    }

    public function getCreateAt(): ?DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(?DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }
    
       public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }
    
       public function getCreatedBy() {
        return $this->createdBy;
    }

    public function getDeletedAt() {
        return $this->deletedAt;
    }

    public function getCreatedFromIp() {
        return $this->createdFromIp;
    }

    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    public function getUpdatedFromIp() {
        return $this->updatedFromIp;
    }

    public function getDeletedBy() {
        return $this->deletedBy;
    }

    public function getDeletedFromIp() {
        return $this->deletedFromIp;
    }

    public function getEditable() {
        return $this->editable;
    }

    public function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function setCreatedFromIp($createdFromIp) {
        $this->createdFromIp = $createdFromIp;
        return $this;
    }

    public function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function setUpdatedFromIp($updatedFromIp) {
        $this->updatedFromIp = $updatedFromIp;
        return $this;
    }

    public function setDeletedFromIp($deletedFromIp) {
        $this->deletedFromIp = $deletedFromIp;
        return $this;
    }

    public function setEditable($editable) {
        $this->editable = $editable;
        return $this;
    }

    public function setDeletedAt($deletedAt) {
        $this->deletedAt = $deletedAt;
        return $this;
    }
    
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }
}
