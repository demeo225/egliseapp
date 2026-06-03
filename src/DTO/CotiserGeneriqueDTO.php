<?php

namespace App\DTO;

class CotiserGeneriqueDTO
{
    private array $paiements = [];
    private int $totalMontantPaye = 0;
    private int $nombrePaiements = 0;
    private float $moyennePaiement = 0;

    public function __construct(?array $paiements = null)
    {
        if ($paiements !== null) {
            $this->paiements = $paiements;
            $this->calculerStatistiques();
        }
    }

    private function calculerStatistiques(): void
    {
        $this->nombrePaiements = count($this->paiements);
        
        foreach ($this->paiements as $paiement) {
            $this->totalMontantPaye += $paiement->getMontantpayer() ?? 0;
        }
        
        $this->moyennePaiement = $this->nombrePaiements > 0 ? $this->totalMontantPaye / $this->nombrePaiements : 0;
    }

    public function getPaiements(): array { return $this->paiements; }
    public function getTotalMontantPaye(): int { return $this->totalMontantPaye; }
    public function getNombrePaiements(): int { return $this->nombrePaiements; }
    public function getMoyennePaiement(): float { return $this->moyennePaiement; }
    public function isEmpty(): bool { return empty($this->paiements); }
}