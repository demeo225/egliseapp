<?php

namespace App\DTO;

class CotisationGroupeDTO
{
    private array $cotisations = [];
    private int $totalCotisations = 0;
    private int $totalMontantPrevus = 0;
    private int $totalMontantPayes = 0;
    private int $totalReste = 0;
    private array $statistiquesParGroupe = [];
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
            
            $cotisergroupes = $cotisation->getCotisergroupes();
            $montantPayePourCetteCotisation = 0;
            
            foreach ($cotisergroupes as $paiement) {
                if ($paiement->getDeletedAt() === null) {
                    $montantPayePourCetteCotisation += $paiement->getMontantpayer() ?? 0;
                }
            }
            
            $restePourCetteCotisation = $montantPrevu - $montantPayePourCetteCotisation;
            
            $this->totalMontantPayes += $montantPayePourCetteCotisation;
            $this->totalReste += max(0, $restePourCetteCotisation);
            
            $groupe = $cotisation->getGroupe();
            $groupeNom = $groupe ? $groupe->getNom() : 'Non défini';
            
            if (!isset($this->statistiquesParGroupe[$groupeNom])) {
                $this->statistiquesParGroupe[$groupeNom] = [
                    'id' => $groupe ? $groupe->getId() : null,
                    'nombre_cotisations' => 0,
                    'montant_prevu' => 0,
                    'montant_paye' => 0,
                    'reste' => 0,
                    'taux_realisation' => 0,
                    'nombre_paiements' => 0
                ];
            }
            
            $this->statistiquesParGroupe[$groupeNom]['nombre_cotisations']++;
            $this->statistiquesParGroupe[$groupeNom]['montant_prevu'] += $montantPrevu;
            $this->statistiquesParGroupe[$groupeNom]['montant_paye'] += $montantPayePourCetteCotisation;
            $this->statistiquesParGroupe[$groupeNom]['reste'] += max(0, $restePourCetteCotisation);
            $this->statistiquesParGroupe[$groupeNom]['nombre_paiements'] += $cotisergroupes->count();
            
            if ($this->statistiquesParGroupe[$groupeNom]['montant_prevu'] > 0) {
                $this->statistiquesParGroupe[$groupeNom]['taux_realisation'] = 
                    round(($this->statistiquesParGroupe[$groupeNom]['montant_paye'] / 
                    $this->statistiquesParGroupe[$groupeNom]['montant_prevu']) * 100, 2);
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
    public function getStatistiquesParGroupe(): array { return $this->statistiquesParGroupe; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
}