<?php

namespace App\DTO;

class BilanZoneDTO extends BilanGeneriqueDTO
{
    private ?int $zoneId = null;
    private ?string $zoneNom = null;
    private ?array $zone = null;

    public function __construct(
        ?array $activites = null,
        ?array $cotisations = null,
        ?array $depenses = null,
        ?array $paiements = null,
        ?array $presences = null,
        ?array $zone = null
    ) {
        parent::__construct($activites, $cotisations, $depenses, $paiements, $presences, 'zone');
        
        if ($zone !== null) {
            $this->zone = $zone;
            if (!empty($zone)) {
                $this->zoneId = $zone[0]->getId() ?? null;
                $this->zoneNom = $zone[0]->getNom() ?? null;
            }
        }
    }
    
    public function getEntiteNom(): string 
    { 
        return $this->zoneNom ?? 'Toutes les zones'; 
    }
    
    public function getEntiteId(): ?int 
    { 
        return $this->zoneId; 
    }
    
    public function getZoneId(): ?int 
    { 
        return $this->zoneId; 
    }
    
    public function getZoneNom(): ?string 
    { 
        return $this->zoneNom; 
    }
    
    public function getZone(): ?array 
    { 
        return $this->zone; 
    }
}