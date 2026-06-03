<?php

namespace App\DTO;

class DepenseDepartementDTO
{
    private array $depenses = [];
    private int $totalDepenses = 0;
    private int $totalMontantDepenses = 0;
    private array $statistiquesParDepartement = [];
    private array $statistiquesParMois = [];
    private array $statistiquesParType = [];

    public function __construct(?array $depenses = null)
    {
        if ($depenses !== null) {
            $this->depenses = $depenses;
            $this->calculerStatistiques();
        }
    }

    private function calculerStatistiques(): void
    {
        $this->totalDepenses = count($this->depenses);
        
        foreach ($this->depenses as $depense) {
            $montant = $depense->getMontant() ?? 0;
            $this->totalMontantDepenses += $montant;
            
            // Statistiques par département
            $departementNom = $depense->getDepartement() ? $depense->getDepartement()->getNom() : 'Non défini';
            if (!isset($this->statistiquesParDepartement[$departementNom])) {
                $this->statistiquesParDepartement[$departementNom] = [
                    'nombre_depenses' => 0,
                    'montant_total' => 0,
                    'moyenne' => 0
                ];
            }
            $this->statistiquesParDepartement[$departementNom]['nombre_depenses']++;
            $this->statistiquesParDepartement[$departementNom]['montant_total'] += $montant;
            
            // Statistiques par type de dépense (basé sur typeoff ou autre)
            $typeDepense = $depense->getTypeoff() === true ? 'Dépense principale' : 'Dépense secondaire';
            if (!isset($this->statistiquesParType[$typeDepense])) {
                $this->statistiquesParType[$typeDepense] = [
                    'nombre_depenses' => 0,
                    'montant_total' => 0,
                    'moyenne' => 0
                ];
            }
            $this->statistiquesParType[$typeDepense]['nombre_depenses']++;
            $this->statistiquesParType[$typeDepense]['montant_total'] += $montant;
            
            // Statistiques par mois
            if ($depense->getDatedepense()) {
                $mois = $depense->getDatedepense()->format('Y-m');
                $moisLibelle = $depense->getDatedepense()->format('F Y');
                if (!isset($this->statistiquesParMois[$mois])) {
                    $this->statistiquesParMois[$mois] = [
                        'libelle' => $moisLibelle,
                        'nombre_depenses' => 0,
                        'montant_total' => 0,
                        'moyenne' => 0
                    ];
                }
                $this->statistiquesParMois[$mois]['nombre_depenses']++;
                $this->statistiquesParMois[$mois]['montant_total'] += $montant;
            }
        }
        
        // Calcul des moyennes par département
        foreach ($this->statistiquesParDepartement as &$stat) {
            if ($stat['nombre_depenses'] > 0) {
                $stat['moyenne'] = $stat['montant_total'] / $stat['nombre_depenses'];
            }
        }
        
        // Calcul des moyennes par type
        foreach ($this->statistiquesParType as &$stat) {
            if ($stat['nombre_depenses'] > 0) {
                $stat['moyenne'] = $stat['montant_total'] / $stat['nombre_depenses'];
            }
        }
        
        // Calcul des moyennes par mois
        foreach ($this->statistiquesParMois as &$stat) {
            if ($stat['nombre_depenses'] > 0) {
                $stat['moyenne'] = $stat['montant_total'] / $stat['nombre_depenses'];
            }
        }
    }

    // Getters
    public function getDepenses(): array { return $this->depenses; }
    public function getTotalDepenses(): int { return $this->totalDepenses; }
    public function getTotalMontantDepenses(): int { return $this->totalMontantDepenses; }
    public function getMoyenneParDepense(): float 
    { 
        return $this->totalDepenses > 0 ? $this->totalMontantDepenses / $this->totalDepenses : 0;
    }
    
    public function isEmpty(): bool { return empty($this->depenses); }
    public function getStatistiquesParDepartement(): array { return $this->statistiquesParDepartement; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
    public function getStatistiquesParType(): array { return $this->statistiquesParType; }
    
    public function getResume(): string
    {
        if ($this->isEmpty()) {
            return 'Aucune dépense disponible';
        }
        
        return sprintf(
            '%d dépense(s) | Total: %d FCFA | Moyenne: %d FCFA',
            $this->totalDepenses,
            $this->totalMontantDepenses,
            (int)$this->getMoyenneParDepense()
        );
    }
}