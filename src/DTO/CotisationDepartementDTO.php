<?php

namespace App\DTO;

class CotisationDepartementDTO
{
    private array $cotisations = [];
    private int $totalCotisations = 0;
    private int $totalMontantPrevus = 0;
    private int $totalMontantPayes = 0;
    private int $totalReste = 0;
    private array $statistiquesParDepartement = [];
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
            // Montant prévu (celui de la cotisation)
            $montantPrevu = $cotisation->getMontant() ?? 0;
            $this->totalMontantPrevus += $montantPrevu;
            
            // Récupérer tous les paiements (Cotiserdepartement) liés à cette cotisation
            $cotiserdepartements = $cotisation->getCotiserdepartements();
            
            $montantPayePourCetteCotisation = 0;
            $restePourCetteCotisation = 0;
            
            // Calculer la somme des montants payés à partir des Cotiserdepartement
            foreach ($cotiserdepartements as $paiement) {
                if ($paiement->getDeletedAt() === null) {
                    $montantPayePourCetteCotisation += $paiement->getMontantpayer() ?? 0;
                }
            }
            
            // Calcul du reste pour cette cotisation (peut être négatif en cas de trop-perçu)
            $restePourCetteCotisation = $montantPrevu - $montantPayePourCetteCotisation;
            
            // Mettre à jour la cotisation avec les valeurs calculées
            $cotisation->montantPaye = $montantPayePourCetteCotisation;
            $cotisation->resteCalcule = $restePourCetteCotisation;
            
            // Accumuler les totaux généraux
            $this->totalMontantPayes += $montantPayePourCetteCotisation;
            $this->totalReste += $restePourCetteCotisation;
            
            // Statistiques par département
            $departement = $cotisation->getDepartement();
            $departementNom = $departement ? $departement->getNom() : 'Non défini';
            
            if (!isset($this->statistiquesParDepartement[$departementNom])) {
                $this->statistiquesParDepartement[$departementNom] = [
                    'id' => $departement ? $departement->getId() : null,
                    'nombre_cotisations' => 0,
                    'montant_prevu' => 0,
                    'montant_paye' => 0,
                    'reste' => 0,
                    'taux_realisation' => 0,
                    'nombre_paiements' => 0
                ];
            }
            
            $this->statistiquesParDepartement[$departementNom]['nombre_cotisations']++;
            $this->statistiquesParDepartement[$departementNom]['montant_prevu'] += $montantPrevu;
            $this->statistiquesParDepartement[$departementNom]['montant_paye'] += $montantPayePourCetteCotisation;
            $this->statistiquesParDepartement[$departementNom]['reste'] += max(0, $restePourCetteCotisation);
            $this->statistiquesParDepartement[$departementNom]['nombre_paiements'] += $cotiserdepartements->count();
            
            // Calcul du taux de réalisation par département
            if ($this->statistiquesParDepartement[$departementNom]['montant_prevu'] > 0) {
                $this->statistiquesParDepartement[$departementNom]['taux_realisation'] = 
                    round(($this->statistiquesParDepartement[$departementNom]['montant_paye'] / 
                    $this->statistiquesParDepartement[$departementNom]['montant_prevu']) * 100, 2);
            }
            
            // Statistiques par mois
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

    // Getters
    public function getCotisations(): array 
    { 
        return $this->cotisations; 
    }
    
    public function getTotalCotisations(): int 
    { 
        return $this->totalCotisations; 
    }
    
    public function getTotalMontantPrevus(): int 
    { 
        return $this->totalMontantPrevus; 
    }
    
    public function getTotalMontantPayes(): int 
    { 
        return $this->totalMontantPayes; 
    }
    
    public function getTotalReste(): int 
    { 
        return max(0, $this->totalReste); 
    }
    
    public function getTauxRealisationGlobal(): float 
    { 
        return $this->totalMontantPrevus > 0 
            ? round(($this->totalMontantPayes / $this->totalMontantPrevus) * 100, 2) 
            : 0;
    }
    
    public function isEmpty(): bool 
    { 
        return empty($this->cotisations); 
    }
    
    public function getStatistiquesParDepartement(): array 
    { 
        return $this->statistiquesParDepartement; 
    }
    
    public function getStatistiquesParMois(): array 
    { 
        return $this->statistiquesParMois; 
    }
}