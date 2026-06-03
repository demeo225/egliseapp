<?php

namespace App\DTO;

class BilanFamilleDTO extends BilanGeneriqueDTO
{
    private ?int $familleId = null;
    private ?string $familleNom = null;
    private ?array $famille = null;

    public function __construct(
        ?array $activites = null,
        ?array $cotisations = null,
        ?array $depenses = null,
        ?array $paiements = null,
        ?array $presences = null,
        ?array $famille = null
    ) {
        parent::__construct($activites, $cotisations, $depenses, $paiements, $presences, 'famille');
        
        if ($famille !== null) {
            $this->famille = $famille;
            if (!empty($famille)) {
                $this->familleId = $famille[0]->getId() ?? null;
                $this->familleNom = $famille[0]->getNom() ?? null;
            }
        }
    }
    
    public function getEntiteNom(): string 
    { 
        return $this->familleNom ?? 'Toutes les familles'; 
    }
    
    public function getEntiteId(): ?int 
    { 
        return $this->familleId; 
    }
    
    public function getFamilleId(): ?int 
    { 
        return $this->familleId; 
    }
    
    public function getFamilleNom(): ?string 
    { 
        return $this->familleNom; 
    }
    
    public function getFamille(): ?array 
    { 
        return $this->famille; 
    }
}