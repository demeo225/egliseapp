<?php

namespace App\DTO;

use App\Entity\Presencedepartement;
use App\Entity\Seancedepartement;
use App\Entity\Departement;
use App\Entity\Fidele;
use DateTimeInterface;

class PresenceDepartementDTO
{
    private ?int $id = null;
    private ?int $fideleId = null;
    private ?string $fideleNom = null;
   // private ?string $fidelePrenom = null;
    private ?string $fideleContact1 = null;
    private ?int $departementId = null;
    private ?string $departementNom = null;
    private ?int $seanceId = null;
    private ?string $seanceLieu = null;
    private ?DateTimeInterface $seanceDate = null;
    private ?int $seanceOffrande = null;
    private ?int $seanceHommes = null;
    private ?int $seanceFemmes = null;
    private ?int $seanceEnfants = null;
    private ?DateTimeInterface $datePresence = null;
    private ?string $periode = null;
    
    // Statistiques
    private int $nombrePresences = 1;
    private array $presencesParMois = [];
    private ?float $tauxPresence = null;
    
    public function __construct(?Presencedepartement $presence = null)
    {
        if ($presence !== null) {
            $this->hydrateFromEntity($presence);
        }
    }
    
    /**
     * Hydrate le DTO à partir d'une entité Presencedepartement
     */
    public function hydrateFromEntity(Presencedepartement $presence): self
    {
        $this->id = $presence->getId();
        
        // Récupérer les informations du fidèle
        $fidele = $presence->getFidele();
        if ($fidele) {
            $this->fideleId = $fidele->getId();
            $this->fideleNom = $fidele->getNomfidele();
         //   $this->fidelePrenom = $fidele->getPrenomfidele();
            $this->fideleContact = $fidele->getContact1();
        }
        
        // Récupérer les informations du département
        $departement = $presence->getDepartement();
        if ($departement) {
            $this->departementId = $departement->getId();
            $this->departementNom = $departement->getNom();
        }
        
        // Récupérer les informations de la séance
        $seance = $presence->getSeancedepartement();
        if ($seance) {
            $this->seanceId = $seance->getId();
            $this->seanceLieu = $seance->getLieu();
            $this->seanceDate = $seance->getDatesuper();
            $this->seanceOffrande = $seance->getOffrande();
            $this->seanceHommes = $seance->getNbrepresent();
            $this->seanceFemmes = $seance->getFemme();
            $this->seanceEnfants = $seance->getEnfant();
            
            // Définir la date de présence (utilise la date de la séance)
            $this->datePresence = $seance->getDatesuper();
            
            // Définir la période
            if ($this->datePresence) {
                $this->periode = $this->datePresence->format('F Y');
            }
        }
        
        return $this;
    }
    
    /**
     * Crée un tableau de DTOs à partir d'une collection d'entités
     */
    public static function createFromCollection(array $presences): array
    {
        $dtos = [];
        $compteurPresences = [];
        
        // Premier passage : compter les présences par fidèle et par séance
        foreach ($presences as $presence) {
            $fideleId = $presence->getFidele()?->getId();
            $seanceId = $presence->getSeancedepartement()?->getId();
            $key = $fideleId . '_' . $seanceId;
            
            if (!isset($compteurPresences[$key])) {
                $compteurPresences[$key] = 0;
            }
            $compteurPresences[$key]++;
        }
        
        // Deuxième passage : créer les DTOs avec les compteurs
        foreach ($presences as $presence) {
            $dto = new self($presence);
            
            $fideleId = $presence->getFidele()?->getId();
            $seanceId = $presence->getSeancedepartement()?->getId();
            $key = $fideleId . '_' . $seanceId;
            
            if (isset($compteurPresences[$key])) {
                $dto->setNombrePresences($compteurPresences[$key]);
            }
            
            $dtos[] = $dto;
        }
        
        return $dtos;
    }
    
    /**
     * Groupe les présences par fidèle
     */
    public static function groupByFidele(array $presences): array
    {
        $grouped = [];
        
        foreach ($presences as $presence) {
            $fideleId = $presence->getFidele()?->getId();
            if ($fideleId && !isset($grouped[$fideleId])) {
                $grouped[$fideleId] = [
                    'fidele_id' => $fideleId,
                    'fidele_nom' => $presence->getFidele()?->getNomfidele(),
                    'fidele_prenom' => $presence->getFidele()?->getContact1(),
                    'departement_nom' => $presence->getDepartement()?->getNom(),
                    'presences' => [],
                    'total_presences' => 0,
                    'derniere_presence' => null
                ];
            }
            
            if ($fideleId) {
                $grouped[$fideleId]['presences'][] = $presence;
                $grouped[$fideleId]['total_presences']++;
                
                $datePresence = $presence->getSeancedepartement()?->getDatesuper();
                if ($datePresence && (!$grouped[$fideleId]['derniere_presence'] || $datePresence > $grouped[$fideleId]['derniere_presence'])) {
                    $grouped[$fideleId]['derniere_presence'] = $datePresence;
                }
            }
        }
        
        return $grouped;
    }
    
    /**
     * Calcule les statistiques des présences par département
     */
    public static function calculerStatistiquesParDepartement(array $presences): array
    {
        $statistiques = [];
        
        foreach ($presences as $presence) {
            $departementId = $presence->getDepartement()?->getId();
            $departementNom = $presence->getDepartement()?->getNom() ?? 'Non défini';
            
            if (!isset($statistiques[$departementId])) {
                $statistiques[$departementId] = [
                    'departement_id' => $departementId,
                    'departement_nom' => $departementNom,
                    'total_presences' => 0,
                    'fideles_distincts' => [],
                    'seances_distinctes' => []
                ];
            }
            
            $statistiques[$departementId]['total_presences']++;
            
            $fideleId = $presence->getFidele()?->getId();
            if ($fideleId) {
                $statistiques[$departementId]['fideles_distincts'][$fideleId] = true;
            }
            
            $seanceId = $presence->getSeancedepartement()?->getId();
            if ($seanceId) {
                $statistiques[$departementId]['seances_distinctes'][$seanceId] = true;
            }
        }
        
        // Convertir les tableaux en compteurs
        foreach ($statistiques as &$stat) {
            $stat['nb_fideles_distincts'] = count($stat['fideles_distincts']);
            $stat['nb_seances_distinctes'] = count($stat['seances_distinctes']);
            unset($stat['fideles_distincts'], $stat['seances_distinctes']);
            
            $stat['moyenne_presences_par_seance'] = $stat['nb_seances_distinctes'] > 0 
                ? round($stat['total_presences'] / $stat['nb_seances_distinctes'], 2) 
                : 0;
        }
        
        return $statistiques;
    }
    
    // Getters
    
    public function getId(): ?int { return $this->id; }
    public function getFideleId(): ?int { return $this->fideleId; }
    public function getFideleNom(): ?string { return $this->fideleNom; }
 //   public function getFidelePrenom(): ?string { return $this->fidelePrenom; }
    public function getFideleNomComplet(): ?string { 
        return $this->fideleNom ? $this->fideleNom  : null;
    }
    public function getFideleContact(): ?string { return $this->fideleContact; }
    public function getDepartementId(): ?int { return $this->departementId; }
    public function getDepartementNom(): ?string { return $this->departementNom; }
    public function getSeanceId(): ?int { return $this->seanceId; }
    public function getSeanceLieu(): ?string { return $this->seanceLieu; }
    public function getSeanceDate(): ?DateTimeInterface { return $this->seanceDate; }
    public function getSeanceOffrande(): ?int { return $this->seanceOffrande; }
    public function getSeanceHommes(): ?int { return $this->seanceHommes; }
    public function getSeanceFemmes(): ?int { return $this->seanceFemmes; }
    public function getSeanceEnfants(): ?int { return $this->seanceEnfants; }
    public function getSeanceTotal(): int { 
        return ($this->seanceHommes ?? 0) + ($this->seanceFemmes ?? 0) + ($this->seanceEnfants ?? 0);
    }
    public function getDatePresence(): ?DateTimeInterface { return $this->datePresence; }
    public function getPeriode(): ?string { return $this->periode; }
    public function getNombrePresences(): int { return $this->nombrePresences; }
    public function getTauxPresence(): ?float { return $this->tauxPresence; }
    
    // Setters
    public function setNombrePresences(int $nombrePresences): self { 
        $this->nombrePresences = $nombrePresences; 
        return $this;
    }
    public function setTauxPresence(?float $tauxPresence): self { 
        $this->tauxPresence = $tauxPresence; 
        return $this;
    }
    
    // Méthodes utilitaires
    public function getDatePresenceFormatee(): string {
        return $this->datePresence ? $this->datePresence->format('d/m/Y') : 'Non définie';
    }
    
    public function getSeanceDateFormatee(): string {
        return $this->seanceDate ? $this->seanceDate->format('d/m/Y') : 'Non définie';
    }
    
    public function getSeanceOffrandeFormatee(): string {
        return number_format($this->seanceOffrande ?? 0, 0, ',', ' ') . ' FCFA';
    }
    
    public function getStatutPresence(): array {
        if ($this->nombrePresences >= 4) {
            return ['label' => 'Très assidu', 'class' => 'success', 'icon' => 'fas fa-star'];
        } elseif ($this->nombrePresences >= 2) {
            return ['label' => 'Assidu', 'class' => 'info', 'icon' => 'fas fa-thumbs-up'];
        } elseif ($this->nombrePresences >= 1) {
            return ['label' => 'Occasionnel', 'class' => 'warning', 'icon' => 'fas fa-calendar-week'];
        } else {
            return ['label' => 'Inactif', 'class' => 'danger', 'icon' => 'fas fa-ban'];
        }
    }
    
    public function estPresentPourSeance(int $seanceId): bool {
        return $this->seanceId === $seanceId;
    }
}