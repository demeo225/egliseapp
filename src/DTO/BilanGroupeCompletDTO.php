<?php

namespace App\DTO;

use App\DTO\BilanGroupeDTO;
use App\DTO\CotisationGroupeDTO;
use App\DTO\DepenseGroupeDTO;
use App\DTO\CotiserGroupeDTO;
use App\DTO\PresenceGroupeDTO;

class BilanGroupeCompletDTO
{
    private ?BilanGroupeDTO $bilanActivites = null;
    private ?CotisationGroupeDTO $bilanCotisations = null;
    private ?DepenseGroupeDTO $bilanDepenses = null;
    private ?CotiserGroupeDTO $bilanPaiements = null; // Ajout de la propriété
  
    private array $presences = [];
    private int $totalPresences = 0;
    private int $fidelesDistincts = 0;
    private float $moyennePresencesParFidele = 0;
    private array $activites = [];
    private array $cotisations = [];
    private array $depenses = [];
    private array $paiements = []; // Pour stocker les Cotisergroupe
    private array $cotisergroupeDetails = [];
    private ?array $selectedCotisergroupe = null;
    private ?float $totalMontantCotisergroupe = null;

    private int $seancesAvecPresences = 0;  // Ajout de cette propriété
    private array $presencesGroupees = [];   // Ajout pour les présences groupées
    private array $statistiquesPresencesParGroupe = [];

    public function __construct(?array $activites = null, ?array $cotisations = null, ?array $depenses = null, ?array $paiements = null ,?array $presences = null)
    {
        if ($activites !== null) {
            $this->activites = $activites;
            $this->bilanActivites = new BilanGroupeDTO($activites);
        }
         if ($presences !== null) {
            $this->presences = $presences;
            $this->calculerStatistiquesPresences();
        }
        if ($cotisations !== null) {
            $this->cotisations = $cotisations;
            $this->bilanCotisations = new CotisationGroupeDTO($cotisations);
        }
        
        if ($depenses !== null) {
            $this->depenses = $depenses;
            $this->bilanDepenses = new DepenseGroupeDTO($depenses);
        }
        
        if ($paiements !== null) {
            $this->paiements = $paiements;
            $this->bilanPaiements = new CotiserGroupeDTO($paiements);
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
            if ($presence->getSeancegroupe()) {
                $seancesIds[$presence->getSeancegroupe()->getId()] = true;
            }
        }
        
        $this->fidelesDistincts = count($fidelesIds);
        $this->seancesAvecPresences = count($seancesIds);  // Calcul des séances avec présences
        
        $this->moyennePresencesParFidele = $this->fidelesDistincts > 0 
            ? round($this->totalPresences / $this->fidelesDistincts, 2) 
            : 0; // Grouper les présences par fidèle
        $this->presencesGroupees = PresenceGroupeDTO::groupByFidele($this->presences);
        
        // Calculer les statistiques par groupe
        $this->statistiquesPresencesParGroupe = PresenceGroupeDTO::calculerStatistiquesParGroupe($this->presences);
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
    public function getBilanActivites(): ?BilanGroupeDTO 
    { 
        return $this->bilanActivites; 
    }
    
    public function getBilanCotisations(): ?CotisationGroupeDTO 
    { 
        return $this->bilanCotisations; 
    }
    
    public function getBilanDepenses(): ?DepenseGroupeDTO 
    { 
        return $this->bilanDepenses; 
    }
    
    /**
     * Getter pour bilanPaiements
     */
    public function getBilanPaiements(): ?CotiserGroupeDTO
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
        $this->bilanPaiements = new CotiserGroupeDTO($paiements);
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
    
    // Getters/Setters pour Cotisergroupe
    public function getCotisergroupeDetails(): array 
    { 
        return $this->cotisergroupeDetails; 
    }
    
    public function setCotisergroupeDetails(array $cotisergroupeDetails): self 
    { 
        $this->cotisergroupeDetails = $cotisergroupeDetails;
        $this->calculerTotalMontant();
        return $this;
    }
    
    public function getSelectedCotisergroupe(): ?array 
    { 
        return $this->selectedCotisergroupe; 
    }
    
    public function setSelectedCotisergroupe(?array $selectedCotisergroupe): self 
    { 
        $this->selectedCotisergroupe = $selectedCotisergroupe; 
        return $this;
    }
    
    public function getTotalMontantCotisergroupe(): ?float
    {
        return $this->totalMontantCotisergroupe;
    }
    
    public function hasCotisergroupeDetails(): bool
    {
        return !empty($this->cotisergroupeDetails);
    }
    
    private function calculerTotalMontant(): void
    {
        $total = 0;
        foreach ($this->cotisergroupeDetails as $detail) {
            if (is_object($detail) && method_exists($detail, 'getMontantpayer')) {
                $total += $detail->getMontantpayer();
            } elseif (is_array($detail) && isset($detail['montantpayer'])) {
                $total += $detail['montantpayer'];
            }
        }
        $this->totalMontantCotisergroupe = $total;
    }
    
    public function addCotisergroupeDetail($detail): self
    {
        $this->cotisergroupeDetails[] = $detail;
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
     * Getter pour statistiquesPresencesParGroupe
     */
    public function getStatistiquesPresencesParGroupe(): array
    {
        return $this->statistiquesPresencesParGroupe;
    }
        
    public function getNombrePaiements(): int
    {
        return count($this->cotisergroupeDetails);
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