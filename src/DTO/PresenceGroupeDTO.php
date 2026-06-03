<?php

namespace App\DTO;

use App\Entity\Presencegroupe;
use DateTimeInterface;

class PresenceGroupeDTO
{
    private array $presences = [];
    private array $presencesGroupees = [];
    private array $statistiquesParGroupe = [];
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
        $groupesStats = [];
        
        foreach ($this->presences as $presence) {
            if ($presence->getFidele()) {
                $fidelesIds[$presence->getFidele()->getId()] = true;
            }
            if ($presence->getSeancegroupe()) {
                $seancesIds[$presence->getSeancegroupe()->getId()] = true;
            }
            
            $groupe = $presence->getGroupe();
            $groupeNom = $groupe ? $groupe->getNom() : 'Non défini';
            $groupeId = $groupe ? $groupe->getId() : null;
            
            if (!isset($groupesStats[$groupeNom])) {
                $groupesStats[$groupeNom] = [
                    'groupe_id' => $groupeId,
                    'groupe_nom' => $groupeNom,
                    'total_presences' => 0,
                    'fideles_distincts' => [],
                    'seances_distinctes' => []
                ];
            }
            
            $groupesStats[$groupeNom]['total_presences']++;
            
            if ($presence->getFidele()) {
                $groupesStats[$groupeNom]['fideles_distincts'][$presence->getFidele()->getId()] = true;
            }
            if ($presence->getSeancegroupe()) {
                $groupesStats[$groupeNom]['seances_distinctes'][$presence->getSeancegroupe()->getId()] = true;
            }
        }
        
        foreach ($groupesStats as &$stat) {
            $stat['nb_fideles_distincts'] = count($stat['fideles_distincts']);
            $stat['nb_seances_distinctes'] = count($stat['seances_distinctes']);
            unset($stat['fideles_distincts'], $stat['seances_distinctes']);
        }
        
        $this->statistiquesParGroupe = $groupesStats;
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
                    $grouped[$fideleId] = [
                        'fidele_id' => $fideleId,
                        'fidele_nom' => $fidele->getNomfidele(),
                        'fidele_prenom' => $fidele->getContact1(),
                        'groupe_nom' => $presence->getGroupe()?->getNom(),
                        'presences' => [],
                        'total_presences' => 0,
                        'derniere_presence' => null
                    ];
                }
                
                $grouped[$fideleId]['total_presences']++;
                
                $datePresence = $presence->getSeancegroupe()?->getDatesuper();
                if ($datePresence && (!$grouped[$fideleId]['derniere_presence'] || $datePresence > $grouped[$fideleId]['derniere_presence'])) {
                    $grouped[$fideleId]['derniere_presence'] = $datePresence;
                }
            }
        }
        
        return $grouped;
    }

    public function getPresences(): array { return $this->presences; }
    public function getPresencesGroupees(): array { return $this->presencesGroupees; }
    public function getStatistiquesParGroupe(): array { return $this->statistiquesParGroupe; }
    public function getTotalPresences(): int { return $this->totalPresences; }
    public function getFidelesDistincts(): int { return $this->fidelesDistincts; }
    public function getMoyennePresencesParFidele(): float { return $this->moyennePresencesParFidele; }
    public function getSeancesAvecPresences(): int { return $this->seancesAvecPresences; }
    public function hasPresences(): bool { return !empty($this->presences); }
}