<?php

namespace App\DTO;

use App\Entity\Culte;
use App\Entity\Fidele;
use App\Entity\Typeculte;
use DateTime;
use Traversable;

class BilanCulteDTO
{
    private array $cultes = [];
    private int $totalCultes = 0;
    private int $totalFideles = 0;
    private float $moyenneParCulte = 0;
    private int $totalHommes = 0;
    private int $totalFemmes = 0;
    private int $totalEnfants = 0;
    private int $totalInvites = 0;
    private array $statistiquesParType = [];
    private array $statistiquesParMois = [];
    
    // Propriétés pour les filtres de recherche
    private ?Typeculte $typeculte = null;
    private ?Fidele $messager = null;
    private ?Fidele $dirigeant = null;
    private ?DateTime $dateDebut = null;
    private ?DateTime $dateFin = null;

    /**
     * Constructeur - prend soit un tableau de cultes, soit des paramètres de recherche
     */
    public function __construct(?array $cultes = null)
    {
        if ($cultes !== null) {
            $this->cultes = $cultes;
            $this->calculerStatistiques();
        }
    }

    /**
     * Calcule toutes les statistiques à partir des cultes
     */
    private function calculerStatistiques(): void
    {
        $this->totalCultes = count($this->cultes);
        
        foreach ($this->cultes as $culte) {
            // Calcul des totaux par catégorie
            $hommes = $culte->getNmbrehomme() ?? 0;
            $femmes = $culte->getNobrefemme() ?? 0;
            $enfants = $culte->getNbrefant() ?? 0;
            $invites = $culte->getInvite() ?? 0;
            
            $this->totalHommes += $hommes;
            $this->totalFemmes += $femmes;
            $this->totalEnfants += $enfants;
            $this->totalInvites += $invites;
            
            $totalCulte = $hommes + $femmes + $enfants + $invites;
            $this->totalFideles += $totalCulte;
            
            // Statistiques par type de culte
            $typeLibelle = $culte->getTypeculte() ? $culte->getTypeculte()->getLibelle() : 'Non défini';
            if (!isset($this->statistiquesParType[$typeLibelle])) {
                $this->statistiquesParType[$typeLibelle] = [
                    'nombre_cultes' => 0,
                    'total_fideles' => 0,
                    'moyenne' => 0
                ];
            }
            $this->statistiquesParType[$typeLibelle]['nombre_cultes']++;
            $this->statistiquesParType[$typeLibelle]['total_fideles'] += $totalCulte;
            
            // Statistiques par mois
            if ($culte->getDateculte()) {
                $mois = $culte->getDateculte()->format('Y-m');
                $moisLibelle = $culte->getDateculte()->format('F Y');
                if (!isset($this->statistiquesParMois[$mois])) {
                    $this->statistiquesParMois[$mois] = [
                        'libelle' => $moisLibelle,
                        'nombre_cultes' => 0,
                        'total_fideles' => 0,
                        'moyenne' => 0
                    ];
                }
                $this->statistiquesParMois[$mois]['nombre_cultes']++;
                $this->statistiquesParMois[$mois]['total_fideles'] += $totalCulte;
            }
        }
        
        // Calcul des moyennes par type
        foreach ($this->statistiquesParType as &$stat) {
            if ($stat['nombre_cultes'] > 0) {
                $stat['moyenne'] = $stat['total_fideles'] / $stat['nombre_cultes'];
            }
        }
        
        // Calcul des moyennes par mois
        foreach ($this->statistiquesParMois as &$stat) {
            if ($stat['nombre_cultes'] > 0) {
                $stat['moyenne'] = $stat['total_fideles'] / $stat['nombre_cultes'];
            }
        }
        
        // Calcul de la moyenne générale
        if ($this->totalCultes > 0) {
            $this->moyenneParCulte = $this->totalFideles / $this->totalCultes;
        }
    }

    /**
     * Ajoute un culte au DTO et recalcule les statistiques
     */
    public function addCulte(Culte $culte): self
    {
        $this->cultes[] = $culte;
        $this->calculerStatistiques();
        return $this;
    }

    /**
     * Définit les cultes et recalcule les statistiques
     */
    public function setCultes(array $cultes): self
    {
        $this->cultes = $cultes;
        $this->calculerStatistiques();
        return $this;
    }

    // ==================== GETTERS PRINCIPAUX ====================
    
    public function getCultes(): array 
    { 
        return $this->cultes; 
    }
    
    public function getTotalCultes(): int 
    { 
        return $this->totalCultes; 
    }
    
    public function getTotalFideles(): int 
    { 
        return $this->totalFideles; 
    }
    
    public function getMoyenneParCulte(): float 
    { 
        return $this->moyenneParCulte; 
    }
    
    public function getTotalHommes(): int
    {
        return $this->totalHommes;
    }
    
    public function getTotalFemmes(): int
    {
        return $this->totalFemmes;
    }
    
    public function getTotalEnfants(): int
    {
        return $this->totalEnfants;
    }
    
    public function getTotalInvites(): int
    {
        return $this->totalInvites;
    }
    
    public function isEmpty(): bool 
    { 
        return empty($this->cultes); 
    }
    
    public function hasResults(): bool
    {
        return !empty($this->cultes);
    }
    
    // ==================== STATISTIQUES DÉTAILLÉES ====================
    
    public function getStatistiquesParType(): array
    {
        return $this->statistiquesParType;
    }
    
    public function getStatistiquesParMois(): array
    {
        return $this->statistiquesParMois;
    }
    
    public function getMeilleurMois(): ?array
    {
        if (empty($this->statistiquesParMois)) {
            return null;
        }
        
        $meilleur = null;
        foreach ($this->statistiquesParMois as $mois) {
            if ($meilleur === null || $mois['total_fideles'] > $meilleur['total_fideles']) {
                $meilleur = $mois;
            }
        }
        return $meilleur;
    }
    
    public function getTypeCultePlusFrequent(): ?array
    {
        if (empty($this->statistiquesParType)) {
            return null;
        }
        
        $plusFrequent = null;
        foreach ($this->statistiquesParType as $type => $stat) {
            if ($plusFrequent === null || $stat['nombre_cultes'] > $plusFrequent['nombre_cultes']) {
                $plusFrequent = array_merge(['nom' => $type], $stat);
            }
        }
        return $plusFrequent;
    }
    
    public function getTauxCroissanceMensuel(): float
    {
        if (count($this->statistiquesParMois) < 2) {
            return 0;
        }
        
        $mois = array_values($this->statistiquesParMois);
        $dernier = end($mois);
        $premier = reset($mois);
        
        if ($premier['total_fideles'] == 0) {
            return 0;
        }
        
        return (($dernier['total_fideles'] - $premier['total_fideles']) / $premier['total_fideles']) * 100;
    }
    
    // ==================== GETTERS POUR LES FILTRES ====================
    
    public function getTypeculte(): ?Typeculte 
    { 
        return $this->typeculte; 
    }
    
    public function setTypeculte(?Typeculte $typeculte): self 
    { 
        $this->typeculte = $typeculte; 
        return $this;
    }
    
    public function getMessager(): ?Fidele 
    { 
        return $this->messager; 
    }
    
    public function setMessager(?Fidele $messager): self 
    { 
        $this->messager = $messager; 
        return $this;
    }
    
    public function getDirigeant(): ?Fidele 
    { 
        return $this->dirigeant; 
    }
    
    public function setDirigeant(?Fidele $dirigeant): self 
    { 
        $this->dirigeant = $dirigeant; 
        return $this;
    }
    
    public function getDateDebut(): ?DateTime 
    { 
        return $this->dateDebut; 
    }
    
    public function setDateDebut(?DateTime $dateDebut): self 
    { 
        $this->dateDebut = $dateDebut; 
        return $this;
    }
    
    public function getDateFin(): ?DateTime 
    { 
        return $this->dateFin; 
    }
    
    public function setDateFin(?DateTime $dateFin): self 
    { 
        $this->dateFin = $dateFin; 
        return $this;
    }
    
    // ==================== MÉTHODES UTILITAIRES ====================
    
    /**
     * Convertit le DTO en tableau pour les API
     */
    public function toArray(): array
    {
        return [
            'total_cultes' => $this->totalCultes,
            'total_fideles' => $this->totalFideles,
            'moyenne_par_culte' => $this->moyenneParCulte,
            'total_hommes' => $this->totalHommes,
            'total_femmes' => $this->totalFemmes,
            'total_enfants' => $this->totalEnfants,
            'total_invites' => $this->totalInvites,
            'statistiques_par_type' => $this->statistiquesParType,
            'statistiques_par_mois' => $this->statistiquesParMois,
            'taux_croissance' => $this->getTauxCroissanceMensuel(),
        ];
    }
    
    /**
     * Retourne un résumé textuel des statistiques
     */
    public function getResume(): string
    {
        if ($this->isEmpty()) {
            return 'Aucune donnée disponible';
        }
        
        return sprintf(
            '%d culte(s) | %d fidèle(s) | Moyenne: %d par culte | %d hommes, %d femmes, %d enfants, %d invités',
            $this->totalCultes,
            $this->totalFideles,
            (int)$this->moyenneParCulte,
            $this->totalHommes,
            $this->totalFemmes,
            $this->totalEnfants,
            $this->totalInvites
        );
    }
}