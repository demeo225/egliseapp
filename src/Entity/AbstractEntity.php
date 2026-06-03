<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AbstractEntity {

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     * @JMS\SerializedName("createdBy")
     * @var \App\Entity\User
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     * @JMS\SerializedName("updatedBy")
     * @var \App\Entity\User
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
     * @var \DateTime Date de suppression
     */
    protected $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     * @JMS\SerializedName("deletedBy")
     * @var \App\Entity\User
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
