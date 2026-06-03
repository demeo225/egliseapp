<?php

namespace App\DTO;

class BilanCelluleDTO extends BilanGeneriqueDTO
{
    private ?int $celluleId = null;
    private ?string $celluleNom = null;
    private ?array $cellule = null;

    public function __construct(
        ?array $activites = null,
        ?array $cotisations = null,
        ?array $depenses = null,
        ?array $paiements = null,
        ?array $presences = null,
        ?array $cellule = null
    ) {
        parent::__construct($activites, $cotisations, $depenses, $paiements, $presences, 'cellule');
        
        if ($cellule !== null) {
            $this->cellule = $cellule;
            if (!empty($cellule)) {
                $this->celluleId = $cellule[0]->getId() ?? null;
                $this->celluleNom = $cellule[0]->getNom() ?? null;
            }
        }
    }
    
    public function getEntiteNom(): string 
    { 
        return $this->celluleNom ?? 'Toutes les cellules'; 
    }
    
    public function getEntiteId(): ?int 
    { 
        return $this->celluleId; 
    }
    
    public function getCelluleId(): ?int 
    { 
        return $this->celluleId; 
    }
    
    public function getCelluleNom(): ?string 
    { 
        return $this->celluleNom; 
    }
    
    public function getCellule(): ?array 
    { 
        return $this->cellule; 
    }
}