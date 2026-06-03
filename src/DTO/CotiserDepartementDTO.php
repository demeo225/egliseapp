<?php

namespace App\DTO;

use App\Entity\Cotiserdepartement;
use App\Entity\Fidele;
use App\Entity\Departement;
use App\Entity\Cotisationdepartement;
use DateTimeInterface;

class CotiserDepartementDTO
{
    private ?int $id = null;
    private ?int $montantPayer = null;
    private ?int $reste = null;
    private ?DateTimeInterface $dateCotiser = null;
    private ?array $fidele = null;
    private ?array $departement = null;
    private ?array $cotisationDepartement = null;
    private ?bool $etat = null;
    private ?DateTimeInterface $createdAt = null;
    private ?DateTimeInterface $updatedAt = null;
    
    // Statistiques
    private ?float $totalMontantPaye = null;
    private ?int $nombrePaiements = null;
    private ?float $moyennePaiement = null;
    private ?float $tauxRemboursement = null;
    
    public function __construct(?array $cotiserdepartements = null)
    {
        if ($cotiserdepartements !== null && !empty($cotiserdepartements)) {
            $this->calculerStatistiques($cotiserdepartements);
        }
    }
    
    /**
     * Crée un DTO à partir d'une entité Cotiserdepartement
     */
    public static function createFromEntity(Cotiserdepartement $cotiserdepartement): self
    {
        $dto = new self();
        
        $dto->id = $cotiserdepartement->getId();
        $dto->montantPayer = $cotiserdepartement->getMontantpayer();
        $dto->reste = $cotiserdepartement->getReste();
        $dto->dateCotiser = $cotiserdepartement->getDatecotiser();
        $dto->etat = $cotiserdepartement->getEtat();
        $dto->createdAt = $cotiserdepartement->getCreateAt();
        $dto->updatedAt = $cotiserdepartement->getUpdateAt();
        
        // Récupérer les informations du fidèle
        if ($cotiserdepartement->getFidele()) {
            $fidele = $cotiserdepartement->getFidele();
            $dto->fidele = [
                'id' => $fidele->getId(),
                'nom' => $fidele->getNomfidele(),
               // 'prenom' => $fidele->getPrenomfidele(),
                'contact1' => $fidele->getContact1(),
               // 'adresse' => $fidele->getAdressefidele(),
            ];
        }
        
        // Récupérer les informations du département
        if ($cotiserdepartement->getDepartement()) {
            $departement = $cotiserdepartement->getDepartement();
            $dto->departement = [
                'id' => $departement->getId(),
                'nom' => $departement->getNom(),
                'description' => $departement->getDescription(),
            ];
        }
        
        // Récupérer les informations de la cotisation
        if ($cotiserdepartement->getCotisationdepartement()) {
            $cotisation = $cotiserdepartement->getCotisationdepartement();
            $dto->cotisationDepartement = [
                'id' => $cotisation->getId(),
                'objet' => $cotisation->getObjet(),
                'montant' => $cotisation->getMontant(),
                'dateFin' => $cotisation->getDatefin()?->format('Y-m-d'),
            ];
        }
        
        return $dto;
    }
    
    /**
     * Crée un tableau de DTOs à partir d'une collection d'entités
     */
    public static function createFromCollection(array $cotiserdepartements): array
    {
        $dtos = [];
        foreach ($cotiserdepartements as $cotiserdepartement) {
            $dtos[] = self::createFromEntity($cotiserdepartement);
        }
        return $dtos;
    }
    
    /**
     * Calcule les statistiques à partir d'une collection
     */
    private function calculerStatistiques(array $cotiserdepartements): void
    {
        $total = 0;
        $this->nombrePaiements = count($cotiserdepartements);
        
        foreach ($cotiserdepartements as $cotiserdepartement) {
            if ($cotiserdepartement instanceof Cotiserdepartement) {
                $total += $cotiserdepartement->getMontantpayer();
            } elseif (is_array($cotiserdepartement) && isset($cotiserdepartement['montantpayer'])) {
                $total += $cotiserdepartement['montantpayer'];
            }
        }
        
        $this->totalMontantPaye = $total;
        $this->moyennePaiement = $this->nombrePaiements > 0 ? $total / $this->nombrePaiements : 0;
    }
    
    /**
     * Calcule le taux de remboursement par rapport à une cotisation
     */
    public function calculerTauxRemboursement(int $montantTotalCotisation): float
    {
        if ($montantTotalCotisation <= 0) {
            return 0;
        }
        
        $this->tauxRemboursement = ($this->montantPayer / $montantTotalCotisation) * 100;
        return $this->tauxRemboursement;
    }
    
    // Getters et Setters
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function getMontantPayer(): ?int
    {
        return $this->montantPayer;
    }
    
    public function setMontantPayer(?int $montantPayer): self
    {
        $this->montantPayer = $montantPayer;
        return $this;
    }
    
    public function getReste(): ?int
    {
        return $this->reste;
    }
    
    public function setReste(?int $reste): self
    {
        $this->reste = $reste;
        return $this;
    }
    
    public function getDateCotiser(): ?DateTimeInterface
    {
        return $this->dateCotiser;
    }
    
    public function setDateCotiser(?DateTimeInterface $dateCotiser): self
    {
        $this->dateCotiser = $dateCotiser;
        return $this;
    }
    
    public function getFidele(): ?array
    {
        return $this->fidele;
    }
    
    public function setFidele(?array $fidele): self
    {
        $this->fidele = $fidele;
        return $this;
    }
    
    public function getFideleNomComplet(): ?string
    {
        if ($this->fidele) {
            return ($this->fidele['nom'] ?? '') . ' ' . ($this->fidele['contact1'] ?? '');
        }
        return null;
    }
    
    public function getDepartement(): ?array
    {
        return $this->departement;
    }
    
    public function setDepartement(?array $departement): self
    {
        $this->departement = $departement;
        return $this;
    }
    
    public function getDepartementNom(): ?string
    {
        return $this->departement ? ($this->departement['nom'] ?? null) : null;
    }
    
    public function getCotisationDepartement(): ?array
    {
        return $this->cotisationDepartement;
    }
    
    public function setCotisationDepartement(?array $cotisationDepartement): self
    {
        $this->cotisationDepartement = $cotisationDepartement;
        return $this;
    }
    
    public function getCotisationObjet(): ?string
    {
        return $this->cotisationDepartement ? ($this->cotisationDepartement['objet'] ?? null) : null;
    }
    
    public function getCotisationMontant(): ?int
    {
        return $this->cotisationDepartement ? ($this->cotisationDepartement['montant'] ?? null) : null;
    }
    
    public function getEtat(): ?bool
    {
        return $this->etat;
    }
    
    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;
        return $this;
    }
    
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    
    public function getTotalMontantPaye(): ?float
    {
        return $this->totalMontantPaye;
    }
    
    public function getNombrePaiements(): ?int
    {
        return $this->nombrePaiements;
    }
    
    public function getMoyennePaiement(): ?float
    {
        return $this->moyennePaiement;
    }
    
    public function getTauxRemboursement(): ?float
    {
        return $this->tauxRemboursement;
    }
    
    // Méthodes utilitaires
    
    public function isRembourse(): bool
    {
        return $this->reste !== null && $this->reste <= 0;
    }
    
    public function isPartiellementRembourse(): bool
    {
        return $this->reste !== null && $this->reste > 0 && $this->montantPayer !== null && $this->montantPayer > 0;
    }
    
    public function getMontantPayerFormate(): string
    {
        return number_format($this->montantPayer ?? 0, 0, ',', ' ') . ' FCFA';
    }
    
    public function getResteFormate(): string
    {
        $reste = $this->reste ?? 0;
        if ($reste < 0) {
            return 'Trop-perçu: ' . number_format(abs($reste), 0, ',', ' ') . ' FCFA';
        }
        return number_format($reste, 0, ',', ' ') . ' FCFA';
    }
    
    public function getDateCotiserFormatee(): string
    {
        return $this->dateCotiser ? $this->dateCotiser->format('d/m/Y') : 'Non définie';
    }
    
    public function getStatutPaiement(): array
    {
        if ($this->isRembourse()) {
            return [
                'label' => 'Remboursé',
                'class' => 'success',
                'icon' => 'fas fa-check-circle'
            ];
        } elseif ($this->isPartiellementRembourse()) {
            return [
                'label' => 'Partiel',
                'class' => 'warning',
                'icon' => 'fas fa-clock'
            ];
        } else {
            return [
                'label' => 'Impayé',
                'class' => 'danger',
                'icon' => 'fas fa-times-circle'
            ];
        }
    }
}