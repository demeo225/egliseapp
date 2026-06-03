<?php

namespace App\DTO;

class DepenseGeneriqueDTO
{
    private array $depenses = [];
    private int $totalDepenses = 0;
    private int $totalMontantDepenses = 0;
    private float $moyenneParDepense = 0;
    private array $statistiquesParEntite = [];
    private array $statistiquesParMois = [];
    private string $entiteType;

    public function __construct(?array $depenses = null, string $entiteType = 'cellule')
    {
        $this->entiteType = $entiteType;
        
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
            
            // Récupération dynamique de l'entité
            $getEntiteMethod = 'get' . ucfirst($this->entiteType);
            $entite = method_exists($depense, $getEntiteMethod) ? $depense->$getEntiteMethod() : null;
            $entiteNom = $entite ? $entite->getNom() : 'Non défini';
            
            if (!isset($this->statistiquesParEntite[$entiteNom])) {
                $this->statistiquesParEntite[$entiteNom] = [
                    'entite_id' => $entite ? $entite->getId() : null,
                    'entite_nom' => $entiteNom,
                    'nombre_depenses' => 0,
                    'montant_total' => 0,
                    'moyenne' => 0
                ];
            }
            
            $this->statistiquesParEntite[$entiteNom]['nombre_depenses']++;
            $this->statistiquesParEntite[$entiteNom]['montant_total'] += $montant;
            $this->statistiquesParEntite[$entiteNom]['moyenne'] = 
                $this->statistiquesParEntite[$entiteNom]['montant_total'] / 
                $this->statistiquesParEntite[$entiteNom]['nombre_depenses'];
            
            // Statistiques par mois
            $dateDepense = $depense->getDatedepense();
            if ($dateDepense) {
                $mois = $dateDepense->format('Y-m');
                $moisLibelle = $dateDepense->format('F Y');
                
                if (!isset($this->statistiquesParMois[$mois])) {
                    $this->statistiquesParMois[$mois] = [
                        'libelle' => $moisLibelle,
                        'nombre_depenses' => 0,
                        'montant_total' => 0
                    ];
                }
                
                $this->statistiquesParMois[$mois]['nombre_depenses']++;
                $this->statistiquesParMois[$mois]['montant_total'] += $montant;
            }
        }
        
        $this->moyenneParDepense = $this->totalDepenses > 0 
            ? $this->totalMontantDepenses / $this->totalDepenses 
            : 0;
    }

    public function getDepenses(): array { return $this->depenses; }
    public function getTotalDepenses(): int { return $this->totalDepenses; }
    public function getTotalMontantDepenses(): int { return $this->totalMontantDepenses; }
    public function getMoyenneParDepense(): float { return $this->moyenneParDepense; }
    public function getStatistiquesParEntite(): array { return $this->statistiquesParEntite; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
    public function isEmpty(): bool { return empty($this->depenses); }
}