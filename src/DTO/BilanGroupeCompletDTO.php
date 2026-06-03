<?php

namespace App\DTO;

use App\DTO\CotisationGroupeDTO;
use App\DTO\CotiserGroupeDTO;
use App\DTO\PresenceGroupeDTO;

class BilanGroupeCompletDTO
{
    private ?CotisationGroupeDTO $bilanCotisations = null;
    private ?CotiserGroupeDTO $bilanPaiements = null;
    private ?PresenceGroupeDTO $bilanPresences = null;
    private array $activites = [];
    private array $cotisations = [];
    private array $depenses = [];
    private array $paiements = [];
    private array $presences = [];
    private array $groupes = [];

    public function __construct(
        ?array $activites = null, 
        ?array $cotisations = null, 
        ?array $depenses = null, 
        ?array $paiements = null,
        ?array $presences = null,
        ?array $groupes = null
    ) {
        if ($activites !== null) {
            $this->activites = $activites;
        }
        
        if ($cotisations !== null) {
            $this->cotisations = $cotisations;
            $this->bilanCotisations = new CotisationGroupeDTO($cotisations);
        }
        
        if ($depenses !== null) {
            $this->depenses = $depenses;
        }
        
        if ($paiements !== null) {
            $this->paiements = $paiements;
            $this->bilanPaiements = new CotiserGroupeDTO($paiements);
        }
        
        if ($presences !== null) {
            $this->presences = $presences;
            $this->bilanPresences = new PresenceGroupeDTO($presences);
        }
        
        if ($groupes !== null) {
            $this->groupes = $groupes;
        }
    }

    public function getBilanCotisations(): ?CotisationGroupeDTO { return $this->bilanCotisations; }
    public function getBilanPaiements(): ?CotiserGroupeDTO { return $this->bilanPaiements; }
    public function getBilanPresences(): ?PresenceGroupeDTO { return $this->bilanPresences; }
    public function getActivites(): array { return $this->activites; }
    public function getCotisations(): array { return $this->cotisations; }
    public function getDepenses(): array { return $this->depenses; }
    public function getPaiements(): array { return $this->paiements; }
    public function getPresences(): array { return $this->presences; }
    public function getGroupes(): array { return $this->groupes; }
    
    public function hasActivites(): bool { return !empty($this->activites); }
    public function hasCotisations(): bool { return !empty($this->cotisations); }
    public function hasDepenses(): bool { return !empty($this->depenses); }
    public function hasPaiements(): bool { return !empty($this->paiements); }
    public function hasPresences(): bool { return !empty($this->presences); }
    public function hasGroupes(): bool { return !empty($this->groupes); }
    
    public function isEmpty(): bool 
    { 
        return empty($this->activites) && empty($this->cotisations) && empty($this->depenses); 
    }
    
    public function getTotalMontantCotisations(): int
    {
        return $this->bilanCotisations ? $this->bilanCotisations->getTotalMontantPayes() : 0;
    }
    
    public function getSoldeGlobal(): float
    {
        return $this->getTotalMontantCotisations() - $this->getTotalMontantDepenses();
    }
    
    private function getTotalMontantDepenses(): float
    {
        $total = 0;
        foreach ($this->depenses as $depense) {
            $total += $depense->getMontant() ?? 0;
        }
        return $total;
    }
}