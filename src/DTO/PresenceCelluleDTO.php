<?php

namespace App\DTO;

use App\Entity\Presencecellule;
use DateTimeInterface;

class PresenceCelluleDTO
{
    private array $presences = [];
    private array $presencesCellulees = [];
    private array $statistiquesParCellule = [];
    private int $totalPresences = 0;
    private int $fidelesDistincts = 0;
    private float $moyennePresencesParFidele = 0;
    private int $seancesAvecPresences = 0;

    public function __construct(?array $presences = null)
    {
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
        $cellulesStats = [];
        
        foreach ($this->presences as $presence) {
            if ($presence->getFidele()) {
                $fidelesIds[$presence->getFidele()->getId()] = true;
            }
            if ($presence->getSeancecellule()) {
                $seancesIds[$presence->getSeancecellule()->getId()] = true;
            }
            
            $cellule = $presence->getCellule();
            $celluleNom = $cellule ? $cellule->getNom() : 'Non défini';
            $celluleId = $cellule ? $cellule->getId() : null;
            
            if (!isset($cellulesStats[$celluleNom])) {
                $cellulesStats[$celluleNom] = [
                    'cellule_id' => $celluleId,
                    'cellule_nom' => $celluleNom,
                    'total_presences' => 0,
                    'fideles_distincts' => [],
                    'seances_distinctes' => []
                ];
            }
            
            $cellulesStats[$celluleNom]['total_presences']++;
            
            if ($presence->getFidele()) {
                $cellulesStats[$celluleNom]['fideles_distincts'][$presence->getFidele()->getId()] = true;
            }
            if ($presence->getSeancecellule()) {
                $cellulesStats[$celluleNom]['seances_distinctes'][$presence->getSeancecellule()->getId()] = true;
            }
        }
        
        foreach ($cellulesStats as &$stat) {
            $stat['nb_fideles_distincts'] = count($stat['fideles_distincts']);
            $stat['nb_seances_distinctes'] = count($stat['seances_distinctes']);
            unset($stat['fideles_distincts'], $stat['seances_distinctes']);
        }
        
        $this->statistiquesParCellule = $cellulesStats;
        $this->fidelesDistincts = count($fidelesIds);
        $this->seancesAvecPresences = count($seancesIds);
        
        $this->moyennePresencesParFidele = $this->fidelesDistincts > 0 
            ? round($this->totalPresences / $this->fidelesDistincts, 2) 
            : 0;
        
        $this->presencesCellulees = $this->groupByFidele();
    }

    private function groupByFidele(): array
    {
        $celluled = [];
        
        foreach ($this->presences as $presence) {
            $fidele = $presence->getFidele();
            if ($fidele) {
                $fideleId = $fidele->getId();
                if (!isset($celluled[$fideleId])) {
                    $celluled[$fideleId] = [
                        'fidele_id' => $fideleId,
                        'fidele_nom' => $fidele->getNomfidele(),
                        'fidele_prenom' => $fidele->getContact1(),
                        'cellule_nom' => $presence->getCellule()?->getNom(),
                        'presences' => [],
                        'total_presences' => 0,
                        'derniere_presence' => null
                    ];
                }
                
                $celluled[$fideleId]['total_presences']++;
                
                $datePresence = $presence->getSeancecellule()?->getDatesuper();
                if ($datePresence && (!$celluled[$fideleId]['derniere_presence'] || $datePresence > $celluled[$fideleId]['derniere_presence'])) {
                    $celluled[$fideleId]['derniere_presence'] = $datePresence;
                }
            }
        }
        
        return $celluled;
    }

    public function getPresences(): array { return $this->presences; }
    public function getPresencesCellulees(): array { return $this->presencesCellulees; }
    public function getStatistiquesParCellule(): array { return $this->statistiquesParCellule; }
    public function getTotalPresences(): int { return $this->totalPresences; }
    public function getFidelesDistincts(): int { return $this->fidelesDistincts; }
    public function getMoyennePresencesParFidele(): float { return $this->moyennePresencesParFidele; }
    public function getSeancesAvecPresences(): int { return $this->seancesAvecPresences; }
    public function hasPresences(): bool { return !empty($this->presences); }
}