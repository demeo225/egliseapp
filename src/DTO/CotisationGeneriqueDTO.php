<?php

namespace App\DTO;

class CotisationGeneriqueDTO
{
    private array $cotisations = [];
    private int $totalCotisations = 0;
    private int $totalMontantPrevus = 0;
    private int $totalMontantPayes = 0;
    private int $totalReste = 0;
    private array $statistiquesParEntite = [];
    private array $statistiquesParMois = [];
    private string $entiteType;

    public function __construct(?array $cotisations = null, string $entiteType = 'cellule')
    {
        $this->entiteType = $entiteType;
        
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
            
            // Récupération des paiements (méthode adaptée selon l'entité)
            $getPaiementsMethod = 'getCotiser' . ucfirst($this->entiteType) . 's';
            $paiements = method_exists($cotisation, $getPaiementsMethod) 
                ? $cotisation->$getPaiementsMethod() 
                : [];
            
            $montantPaye = 0;
            foreach ($paiements as $paiement) {
                if ($paiement->getDeletedAt() === null) {
                    $montantPaye += $paiement->getMontantpayer() ?? 0;
                }
            }
            
            $reste = $montantPrevu - $montantPaye;
            
            $this->totalMontantPayes += $montantPaye;
            $this->totalReste += max(0, $reste);
            
            // Récupération dynamique de l'entité (Cellule, Famille, Zone)
            $getEntiteMethod = 'get' . ucfirst($this->entiteType);
            $entite = method_exists($cotisation, $getEntiteMethod) ? $cotisation->$getEntiteMethod() : null;
            $entiteNom = $entite ? $entite->getNom() : 'Non défini';
            
            if (!isset($this->statistiquesParEntite[$entiteNom])) {
                $this->statistiquesParEntite[$entiteNom] = [
                    'entite_id' => $entite ? $entite->getId() : null,
                    'entite_nom' => $entiteNom,
                    'nombre_cotisations' => 0,
                    'montant_prevu' => 0,
                    'montant_paye' => 0,
                    'reste' => 0,
                    'taux_realisation' => 0
                ];
            }
            
            $this->statistiquesParEntite[$entiteNom]['nombre_cotisations']++;
            $this->statistiquesParEntite[$entiteNom]['montant_prevu'] += $montantPrevu;
            $this->statistiquesParEntite[$entiteNom]['montant_paye'] += $montantPaye;
            $this->statistiquesParEntite[$entiteNom]['reste'] += max(0, $reste);
            
            if ($this->statistiquesParEntite[$entiteNom]['montant_prevu'] > 0) {
                $this->statistiquesParEntite[$entiteNom]['taux_realisation'] = 
                    round(($this->statistiquesParEntite[$entiteNom]['montant_paye'] / 
                    $this->statistiquesParEntite[$entiteNom]['montant_prevu']) * 100, 2);
            }
            
            // Statistiques par mois
            $dateCreation = $cotisation->getCreateAt();
            if ($dateCreation) {
                $mois = $dateCreation->format('Y-m');
                $moisLibelle = $dateCreation->format('F Y');
                
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
                $this->statistiquesParMois[$mois]['montant_paye'] += $montantPaye;
                $this->statistiquesParMois[$mois]['reste'] += max(0, $reste);
                
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
    public function getStatistiquesParEntite(): array { return $this->statistiquesParEntite; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
}