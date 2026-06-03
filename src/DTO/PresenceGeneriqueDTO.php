<?php

namespace App\DTO;

class PresenceGeneriqueDTO
{
    private array $presences = [];
    private array $presencesGroupees = [];
    private array $statistiquesParEntite = [];
    private int $totalPresences = 0;
    private int $fidelesDistincts = 0;
    private float $moyennePresencesParFidele = 0;
    private int $seancesAvecPresences = 0;
    private string $entiteType;

    public function __construct(?array $presences = null, string $entiteType = 'cellule')
    {
        $this->entiteType = $entiteType;
        
        if ($presences !== null) {
            $this->presences = $presences;
            $this->calculerStatistiques();
        }
    }

    private function calculerStatistiques(): void
    {
        $this->totalPresences = count($this->presences);
        
        $fidelesIds = [];
        $seancesIds = [];
        $entitesStats = [];
        
        foreach ($this->presences as $presence) {
            // Récupérer le fidèle
            $fidele = $presence->getFidele();
            if ($fidele) {
                $fidelesIds[$fidele->getId()] = true;
            }
            
            // Récupérer la séance avec méthode dynamique
            $getSeanceMethod = 'getSeance' . ucfirst($this->entiteType);
            $seance = method_exists($presence, $getSeanceMethod) ? $presence->$getSeanceMethod() : null;
            
            if ($seance) {
                $seancesIds[$seance->getId()] = true;
            }
            
            // Récupérer l'entité (cellule/famille/zone) avec méthode dynamique
            $getEntiteMethod = 'get' . ucfirst($this->entiteType);
            $entite = method_exists($presence, $getEntiteMethod) ? $presence->$getEntiteMethod() : null;
            $entiteNom = $entite ? $entite->getNom() : 'Non défini';
            
            if (!isset($entitesStats[$entiteNom])) {
                $entitesStats[$entiteNom] = [
                    'entite_id' => $entite ? $entite->getId() : null,
                    'entite_nom' => $entiteNom,
                    'total_presences' => 0,
                    'fideles_distincts' => []
                ];
            }
            
            $entitesStats[$entiteNom]['total_presences']++;
            
            if ($fidele) {
                $entitesStats[$entiteNom]['fideles_distincts'][$fidele->getId()] = true;
            }
        }
        
        foreach ($entitesStats as &$stat) {
            $stat['nb_fideles_distincts'] = count($stat['fideles_distincts']);
            unset($stat['fideles_distincts']);
        }
        
        $this->statistiquesParEntite = $entitesStats;
        $this->fidelesDistincts = count($fidelesIds);
        $this->seancesAvecPresences = count($seancesIds);
        
        $this->moyennePresencesParFidele = $this->fidelesDistincts > 0 
            ? round($this->totalPresences / $this->fidelesDistincts, 2) 
            : 0;
        
        $this->presencesGroupees = $this->groupByFidele();
    }
    
    private function groupByFidele(): array
    {
        $grouped = [];
        
        foreach ($this->presences as $presence) {
            $fidele = $presence->getFidele();
            if ($fidele) {
                $fideleId = $fidele->getId();
                if (!isset($grouped[$fideleId])) {
                    $getEntiteMethod = 'get' . ucfirst($this->entiteType);
                    $entite = method_exists($presence, $getEntiteMethod) ? $presence->$getEntiteMethod() : null;
                    
                    $grouped[$fideleId] = [
                        'fidele_id' => $fideleId,
                        'fidele_nom' => $fidele->getNomfidele(),
                        'fidele_prenom' => $fidele->getContact1(),
                        'entite_nom' => $entite ? $entite->getNom() : null,
                        'total_presences' => 0,
                        'derniere_presence' => null
                    ];
                }
                
                $grouped[$fideleId]['total_presences']++;
                
                $getSeanceMethod = 'getSeance' . ucfirst($this->entiteType);
                $seance = method_exists($presence, $getSeanceMethod) ? $presence->$getSeanceMethod() : null;
                $datePresence = $seance ? $seance->getDatesuper() : null;
                
                if ($datePresence && (!$grouped[$fideleId]['derniere_presence'] || $datePresence > $grouped[$fideleId]['derniere_presence'])) {
                    $grouped[$fideleId]['derniere_presence'] = $datePresence;
                }
            }
        }
        
        return $grouped;
    }

    public function getPresences(): array { return $this->presences; }
    public function getPresencesGroupees(): array { return $this->presencesGroupees; }
    public function getStatistiquesParEntite(): array { return $this->statistiquesParEntite; }
    public function getTotalPresences(): int { return $this->totalPresences; }
    public function getFidelesDistincts(): int { return $this->fidelesDistincts; }
    public function getMoyennePresencesParFidele(): float { return $this->moyennePresencesParFidele; }
    public function getSeancesAvecPresences(): int { return $this->seancesAvecPresences; }
    public function hasPresences(): bool { return !empty($this->presences); }
}