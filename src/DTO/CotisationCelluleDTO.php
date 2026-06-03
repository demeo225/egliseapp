<?php

namespace App\DTO;

class CotisationCelluleDTO
{
    private array $cotisations = [];
    private int $totalCotisations = 0;
    private int $totalMontantPrevus = 0;
    private int $totalMontantPayes = 0;
    private int $totalReste = 0;
    private array $statistiquesParCellule = [];
    private array $statistiquesParMois = [];

    public function __construct(?array $cotisations = null)
    {
        if ($cotisations !== null) {
            $this->cotisations = $cotisations;
            $this->calculerStatistiques();
        }
    }

    private function calculerStatistiques(): void
    {
        $this->totalCotisations = count($this->cotisations);
        
        foreach ($this->cotisations as $cotisation) {
            $montantPrevu = $cotisation->getMontant() ?? 0;
            $this->totalMontantPrevus += $montantPrevu;
            
            $cotisercellules = $cotisation->getCotisercellules();
            $montantPayePourCetteCotisation = 0;
            
            foreach ($cotisercellules as $paiement) {
                if ($paiement->getDeletedAt() === null) {
                    $montantPayePourCetteCotisation += $paiement->getMontantpayer() ?? 0;
                }
            }
            
            $restePourCetteCotisation = $montantPrevu - $montantPayePourCetteCotisation;
            
            $this->totalMontantPayes += $montantPayePourCetteCotisation;
            $this->totalReste += max(0, $restePourCetteCotisation);
            
            $cellule = $cotisation->getCellule();
            $celluleNom = $cellule ? $cellule->getNom() : 'Non défini';
            
            if (!isset($this->statistiquesParCellule[$celluleNom])) {
                $this->statistiquesParCellule[$celluleNom] = [
                    'id' => $cellule ? $cellule->getId() : null,
                    'nombre_cotisations' => 0,
                    'montant_prevu' => 0,
                    'montant_paye' => 0,
                    'reste' => 0,
                    'taux_realisation' => 0,
                    'nombre_paiements' => 0
                ];
            }
            
            $this->statistiquesParCellule[$celluleNom]['nombre_cotisations']++;
            $this->statistiquesParCellule[$celluleNom]['montant_prevu'] += $montantPrevu;
            $this->statistiquesParCellule[$celluleNom]['montant_paye'] += $montantPayePourCetteCotisation;
            $this->statistiquesParCellule[$celluleNom]['reste'] += max(0, $restePourCetteCotisation);
            $this->statistiquesParCellule[$celluleNom]['nombre_paiements'] += $cotisercellules->count();
            
            if ($this->statistiquesParCellule[$celluleNom]['montant_prevu'] > 0) {
                $this->statistiquesParCellule[$celluleNom]['taux_realisation'] = 
                    round(($this->statistiquesParCellule[$celluleNom]['montant_paye'] / 
                    $this->statistiquesParCellule[$celluleNom]['montant_prevu']) * 100, 2);
            }
            
            if ($cotisation->getCreateAt()) {
                $mois = $cotisation->getCreateAt()->format('Y-m');
                $moisLibelle = $cotisation->getCreateAt()->format('F Y');
                
                if (!isset($this->statistiquesParMois[$mois])) {
                    $this->statistiquesParMois[$mois] = [
                        'libelle' => $moisLibelle,
                        'mois' => $mois,
                        'nombre_cotisations' => 0,
                        'montant_prevu' => 0,
                        'montant_paye' => 0,
                        'reste' => 0,
                        'taux_realisation' => 0
                    ];
                }
                
                $this->statistiquesParMois[$mois]['nombre_cotisations']++;
                $this->statistiquesParMois[$mois]['montant_prevu'] += $montantPrevu;
                $this->statistiquesParMois[$mois]['montant_paye'] += $montantPayePourCetteCotisation;
                $this->statistiquesParMois[$mois]['reste'] += max(0, $restePourCetteCotisation);
                
                if ($this->statistiquesParMois[$mois]['montant_prevu'] > 0) {
                    $this->statistiquesParMois[$mois]['taux_realisation'] = 
                        round(($this->statistiquesParMois[$mois]['montant_paye'] / 
                        $this->statistiquesParMois[$mois]['montant_prevu']) * 100, 2);
                }
            }
        }
    }

    public function getCotisations(): array { return $this->cotisations; }
    public function getTotalCotisations(): int { return $this->totalCotisations; }
    public function getTotalMontantPrevus(): int { return $this->totalMontantPrevus; }
    public function getTotalMontantPayes(): int { return $this->totalMontantPayes; }
    public function getTotalReste(): int { return max(0, $this->totalReste); }
    public function getTauxRealisationGlobal(): float 
    { 
        return $this->totalMontantPrevus > 0 
            ? round(($this->totalMontantPayes / $this->totalMontantPrevus) * 100, 2) 
            : 0;
    }
    public function isEmpty(): bool { return empty($this->cotisations); }
    public function getStatistiquesParCellule(): array { return $this->statistiquesParCellule; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
}