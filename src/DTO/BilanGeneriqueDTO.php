<?php

namespace App\DTO;

abstract class BilanGeneriqueDTO
{
    protected array $activites = [];
    protected array $cotisations = [];
    protected array $depenses = [];
    protected array $paiements = [];
    protected array $presences = [];
    protected ?CotisationGeneriqueDTO $bilanCotisations = null;
    protected ?CotiserGeneriqueDTO $bilanPaiements = null;
    protected ?PresenceGeneriqueDTO $bilanPresences = null;
    protected ?DepenseGeneriqueDTO $bilanDepenses = null;
    
    protected int $totalMontantDepenses = 0;
    protected string $entiteType;
    
    public function __construct(
        ?array $activites = null,
        ?array $cotisations = null,
        ?array $depenses = null,
        ?array $paiements = null,
        ?array $presences = null,
        string $entiteType = 'cellule'
    ) {
        $this->entiteType = $entiteType;
        
        if ($activites !== null) {
            $this->activites = $activites;
        }
        
        if ($cotisations !== null) {
            $this->cotisations = $cotisations;
            $this->bilanCotisations = new CotisationGeneriqueDTO($cotisations, $entiteType);
        }
        
        if ($paiements !== null) {
            $this->paiements = $paiements;
            $this->bilanPaiements = new CotiserGeneriqueDTO($paiements);
        }
        
        if ($presences !== null) {
            $this->presences = $presences;
            $this->bilanPresences = new PresenceGeneriqueDTO($presences, $entiteType);
        }

        if ($depenses !== null) {
            $this->depenses = $depenses;
            $this->bilanDepenses = new DepenseGeneriqueDTO($depenses, $entiteType);
            $this->calculerTotalDepenses();
        }
    }
    
    private function calculerTotalDepenses(): void
    {
        $this->totalMontantDepenses = 0;
        foreach ($this->depenses as $depense) {
            $this->totalMontantDepenses += $depense->getMontant() ?? 0;
        }
    }
    
    // Getters
    public function getActivites(): array { return $this->activites; }
    public function getCotisations(): array { return $this->cotisations; }
    public function getDepenses(): array { return $this->depenses; }
    public function getPaiements(): array { return $this->paiements; }
    public function getPresences(): array { return $this->presences; }
    public function getBilanCotisations(): ?CotisationGeneriqueDTO { return $this->bilanCotisations; }
    public function getBilanPaiements(): ?CotiserGeneriqueDTO { return $this->bilanPaiements; }
    public function getBilanPresences(): ?PresenceGeneriqueDTO { return $this->bilanPresences; }
    public function getBilanDepenses(): ?DepenseGeneriqueDTO { return $this->bilanDepenses; }
    public function getTotalMontantDepenses(): int { return $this->totalMontantDepenses; }
    
    // Tests
    public function hasActivites(): bool { return !empty($this->activites); }
    public function hasCotisations(): bool { return !empty($this->cotisations); }
    public function hasDepenses(): bool { return !empty($this->depenses); }
    public function hasPaiements(): bool { return !empty($this->paiements); }
    public function hasPresences(): bool { return !empty($this->presences); }
    
    public function isEmpty(): bool
    {
        return empty($this->activites) && empty($this->cotisations) && empty($this->depenses);
    }
    
    public function getTotalMontantCotisations(): int
    {
        return $this->bilanCotisations ? $this->bilanCotisations->getTotalMontantPayes() : 0;
    }
    
    public function getSoldeGlobal(): int
    {
        return $this->getTotalMontantCotisations() - $this->totalMontantDepenses;
    }
    
    // Méthodes abstraites
    abstract public function getEntiteNom(): string;
    abstract public function getEntiteId(): ?int;
}