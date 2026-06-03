<?php

namespace App\DTO;

use App\DTO\BilanCelluleDTO;
use App\DTO\CotisationCelluleDTO;
use App\DTO\DepenseCelluleDTO;
use App\DTO\CotiserCelluleDTO;
use App\DTO\PresenceCelluleDTO;

class BilanCelluleCompletDTO
{
    private ?BilanCelluleDTO $bilanActivites = null;
    private ?CotisationCelluleDTO $bilanCotisations = null;
    private ?DepenseCelluleDTO $bilanDepenses = null;
    private ?CotiserCelluleDTO $bilanPaiements = null; // Ajout de la propriété
  
    private array $presences = [];
    private int $totalPresences = 0;
    private int $fidelesDistincts = 0;
    private float $moyennePresencesParFidele = 0;
    private array $activites = [];
    private array $cotisations = [];
    private array $depenses = [];
    private array $paiements = []; // Pour stocker les Cotisercellule
    private array $cotisercelluleDetails = [];
    private ?array $selectedCotisercellule = null;
    private ?float $totalMontantCotisercellule = null;

    private int $seancesAvecPresences = 0;  // Ajout de cette propriété
    private array $presencesGroupees = [];   // Ajout pour les présences groupées
    private array $statistiquesPresencesParCellule = [];

    public function __construct(?array $activites = null, ?array $cotisations = null, ?array $depenses = null, ?array $paiements = null ,?array $presences = null)
    {
        if ($activites !== null) {
            $this->activites = $activites;
            $this->bilanActivites = new BilanCelluleDTO($activites);
        }
         if ($presences !== null) {
            $this->presences = $presences;
            $this->calculerStatistiquesPresences();
        }
        if ($cotisations !== null) {
            $this->cotisations = $cotisations;
            $this->bilanCotisations = new CotisationCelluleDTO($cotisations);
        }
        
        if ($depenses !== null) {
            $this->depenses = $depenses;
            $this->bilanDepenses = new DepenseCelluleDTO($depenses);
        }
        
        if ($paiements !== null) {
            $this->paiements = $paiements;
            $this->bilanPaiements = new CotiserCelluleDTO($paiements);
        }
   if ($presences !== null) {
        $this->setPresences($presences);  // Appel de la méthode setPresences
    }
    }

    

    // Getters principaux
  private function calculerStatistiquesPresences(): void
    {
        $this->totalPresences = count($this->presences);
        
        $fidelesIds = [];
        $seancesIds = [];
        
        foreach ($this->presences as $presence) {
            if ($presence->getFidele()) {
                $fidelesIds[$presence->getFidele()->getId()] = true;
            }
            if ($presence->getSeancecellule()) {
                $seancesIds[$presence->getSeancecellule()->getId()] = true;
            }
        }
        
        $this->fidelesDistincts = count($fidelesIds);
        $this->seancesAvecPresences = count($seancesIds);  // Calcul des séances avec présences
        
        $this->moyennePresencesParFidele = $this->fidelesDistincts > 0 
            ? round($this->totalPresences / $this->fidelesDistincts, 2) 
            : 0; // Grouper les présences par fidèle
        $this->presencesGroupees = PresenceCelluleDTO::groupByFidele($this->presences);
        
        // Calculer les statistiques par département
        $this->statistiquesPresencesParCellule = PresenceCelluleDTO::calculerStatistiquesParCellule($this->presences);
    }

    public function getPresences(): array { return $this->presences; }
    public function getTotalPresences(): int { return $this->totalPresences; }
    public function getFidelesDistincts(): int { return $this->fidelesDistincts; }
    public function getMoyennePresencesParFidele(): float { return $this->moyennePresencesParFidele; }
    public function hasPresences(): bool { return !empty($this->presences); }
    
    public function setPresences(array $presences): self
    {
        $this->presences = $presences;
        $this->calculerStatistiquesPresences();
        return $this;
    }
        public function getSeancesAvecPresences(): int
    {
        return $this->seancesAvecPresences;
    }
    public function getBilanActivites(): ?BilanCelluleDTO 
    { 
        return $this->bilanActivites; 
    }
    
    public function getBilanCotisations(): ?CotisationCelluleDTO 
    { 
        return $this->bilanCotisations; 
    }
    
    public function getBilanDepenses(): ?DepenseCelluleDTO 
    { 
        return $this->bilanDepenses; 
    }
    
    /**
     * Getter pour bilanPaiements
     */
    public function getBilanPaiements(): ?CotiserCelluleDTO
    {
        return $this->bilanPaiements;
    }
    
    public function getActivites(): array 
    { 
        return $this->activites; 
    }
    
    public function getCotisations(): array 
    { 
        return $this->cotisations; 
    }
    
    public function getDepenses(): array 
    { 
        return $this->depenses; 
    }
    
    public function getPaiements(): array
    {
        return $this->paiements;
    }
    
    public function setPaiements(array $paiements): self
    {
        $this->paiements = $paiements;
        $this->bilanPaiements = new CotiserCelluleDTO($paiements);
        return $this;
    }
    
    // Méthodes utilitaires
    public function hasActivites(): bool 
    { 
        return !empty($this->activites); 
    }
    
    public function hasCotisations(): bool 
    { 
        return !empty($this->cotisations); 
    }
    
    public function hasDepenses(): bool 
    { 
        return !empty($this->depenses); 
    }
    
    public function hasPaiements(): bool
    {
        return !empty($this->paiements);
    }
    
    public function hasBilanPaiements(): bool
    {
        return $this->bilanPaiements !== null && !$this->bilanPaiements->isEmpty();
    }
    
    public function isEmpty(): bool 
    { 
        return empty($this->activites) && empty($this->cotisations) && empty($this->depenses); 
    }
    
    // Getters/Setters pour Cotisercellule
    public function getCotisercelluleDetails(): array 
    { 
        return $this->cotisercelluleDetails; 
    }
    
    public function setCotisercelluleDetails(array $cotisercelluleDetails): self 
    { 
        $this->cotisercelluleDetails = $cotisercelluleDetails;
        $this->calculerTotalMontant();
        return $this;
    }
    
    public function getSelectedCotisercellule(): ?array 
    { 
        return $this->selectedCotisercellule; 
    }
    
    public function setSelectedCotisercellule(?array $selectedCotisercellule): self 
    { 
        $this->selectedCotisercellule = $selectedCotisercellule; 
        return $this;
    }
    
    public function getTotalMontantCotisercellule(): ?float
    {
        return $this->totalMontantCotisercellule;
    }
    
    public function hasCotisercelluleDetails(): bool
    {
        return !empty($this->cotisercelluleDetails);
    }
    
    private function calculerTotalMontant(): void
    {
        $total = 0;
        foreach ($this->cotisercelluleDetails as $detail) {
            if (is_object($detail) && method_exists($detail, 'getMontantpayer')) {
                $total += $detail->getMontantpayer();
            } elseif (is_array($detail) && isset($detail['montantpayer'])) {
                $total += $detail['montantpayer'];
            }
        }
        $this->totalMontantCotisercellule = $total;
    }
    
    public function addCotisercelluleDetail($detail): self
    {
        $this->cotisercelluleDetails[] = $detail;
        $this->calculerTotalMontant();
        return $this;
    }

    /**
     * Getter pour presencesGroupees
     */
    public function getPresencesGroupees(): array
    {
        return $this->presencesGroupees;
    }
    
    /**
     * Getter pour statistiquesPresencesParCellule
     */
    public function getStatistiquesPresencesParCellule(): array
    {
        return $this->statistiquesPresencesParCellule;
    }
        
    public function getNombrePaiements(): int
    {
        return count($this->cotisercelluleDetails);
    }
    
    public function getResumeGlobal(): string
    {
        if ($this->isEmpty()) {
            return 'Aucune donnée disponible';
        }
        
        $parts = [];
        if ($this->hasActivites()) {
            $parts[] = count($this->activites) . ' activité(s)';
        }
        if ($this->hasCotisations()) {
            $parts[] = count($this->cotisations) . ' cotisation(s)';
        }
        if ($this->hasDepenses()) {
            $parts[] = count($this->depenses) . ' dépense(s)';
        }
        
        return implode(' | ', $parts);
    }
    
    public function getSoldeGlobal(): float
    {
        $totalCotisations = $this->bilanCotisations ? $this->bilanCotisations->getTotalMontantPayes() : 0;
        $totalDepenses = $this->bilanDepenses ? $this->bilanDepenses->getTotalMontantDepenses() : 0;
        
        return $totalCotisations - $totalDepenses;
    }
    
    public function getSoldeGlobalFormate(): string
    {
        $solde = $this->getSoldeGlobal();
        $couleur = $solde >= 0 ? 'success' : 'danger';
        
        return sprintf(
            '<span class="text-%s">%s FCFA</span>',
            $couleur,
            number_format($solde, 0, ',', ' ')
        );
    }
}