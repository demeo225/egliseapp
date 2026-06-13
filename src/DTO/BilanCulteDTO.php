<?php

namespace App\DTO;

use App\Entity\Culte;
use App\Entity\Fidele;
use App\Entity\Typeculte;
use App\Entity\Presenceculte;
use DateTime;
use IntlDateFormatter;
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
    
    // NOUVEAU: Statistiques de présence individuelle
    private array $presencesParCulte = [];
    private int $totalPresencesEnregistrees = 0;
    private float $moyennePresencesParCulte = 0;
    private int $totalFidelesUniques = 0;
    private array $topFidelesPresent = [];
    private array $presencesParMois = [];
    
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
     * Formate une date en français
     */
    private function formatDateFr(DateTime $date, string $format = 'F Y'): string
    {
        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            null,
            null,
            $format
        );
        return $formatter->format($date);
    }

    /**
     * Calcule toutes les statistiques à partir des cultes
     */
    private function calculerStatistiques(): void
    {
        $this->totalCultes = count($this->cultes);
        
        // Pour le suivi des présences individuelles
        $fidelesPresences = [];
        
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
            
            // Statistiques par mois (version française)
            if ($culte->getDateculte()) {
                $mois = $culte->getDateculte()->format('Y-m');
                $moisLibelleFr = $this->formatDateFr($culte->getDateculte(), 'MMMM YYYY');
                // Première lettre en majuscule
                $moisLibelleFr = ucfirst($moisLibelleFr);
                
                if (!isset($this->statistiquesParMois[$mois])) {
                    $this->statistiquesParMois[$mois] = [
                        'libelle' => $moisLibelleFr,
                        'libelle_en' => $culte->getDateculte()->format('F Y'),
                        'nombre_cultes' => 0,
                        'total_fideles' => 0,
                        'moyenne' => 0
                    ];
                }
                $this->statistiquesParMois[$mois]['nombre_cultes']++;
                $this->statistiquesParMois[$mois]['total_fideles'] += $totalCulte;
            }
            
            // Traitement des présences individuelles
            $presences = $culte->getPresencecultes();
            $nbPresences = count($presences);
            $this->presencesParCulte[$culte->getId()] = $nbPresences;
            $this->totalPresencesEnregistrees += $nbPresences;
            
            // Compter les fidèles uniques
            foreach ($presences as $presence) {
                $fidele = $presence->getFidele();
                if ($fidele && $fidele->getId()) {
                    $fideleId = $fidele->getId();
                    if (!isset($fidelesPresences[$fideleId])) {
                        $fidelesPresences[$fideleId] = [
                            'fidele' => $fidele,
                            'nombre_presences' => 0,
                            'nom' => $fidele->getNomfidele(),
                            'prenom' => $fidele->getContact1() 
                        ];
                    }
                    $fidelesPresences[$fideleId]['nombre_presences']++;
                }
            }
            
            // Statistiques de présence par mois (version française)
            if ($culte->getDateculte()) {
                $mois = $culte->getDateculte()->format('Y-m');
                $moisLibelleFr = $this->formatDateFr($culte->getDateculte(), 'MMMM YYYY');
                $moisLibelleFr = ucfirst($moisLibelleFr);
                
                if (!isset($this->presencesParMois[$mois])) {
                    $this->presencesParMois[$mois] = [
                        'libelle' => $moisLibelleFr,
                        'libelle_en' => $culte->getDateculte()->format('F Y'),
                        'nombre_presences' => 0,
                        'nombre_cultes' => 0,
                        'moyenne_presences' => 0
                    ];
                }
                $this->presencesParMois[$mois]['nombre_presences'] += $nbPresences;
                $this->presencesParMois[$mois]['nombre_cultes']++;
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
            $this->moyennePresencesParCulte = $this->totalPresencesEnregistrees / $this->totalCultes;
        }
        
        // Calcul des moyennes de présence par mois
        foreach ($this->presencesParMois as &$stat) {
            if ($stat['nombre_cultes'] > 0) {
                $stat['moyenne_presences'] = $stat['nombre_presences'] / $stat['nombre_cultes'];
            }
        }
        
        // Top des fidèles les plus présents
        $this->totalFidelesUniques = count($fidelesPresences);
        usort($fidelesPresences, function($a, $b) {
            return $b['nombre_presences'] - $a['nombre_presences'];
        });
        $this->topFidelesPresent = array_slice($fidelesPresences, 0, 10);
    }

    // ... (tous les autres getters et setters restent identiques)
    
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
    
    // ==================== STATISTIQUES DE PRÉSENCE ====================
    
    public function getTotalPresencesEnregistrees(): int
    {
        return $this->totalPresencesEnregistrees;
    }
    
    public function getMoyennePresencesParCulte(): float
    {
        return $this->moyennePresencesParCulte;
    }
    
    public function getTotalFidelesUniques(): int
    {
        return $this->totalFidelesUniques;
    }
    
    public function getTopFidelesPresent(): array
    {
        return $this->topFidelesPresent;
    }
    
    public function getPresencesParCulte(): array
    {
        return $this->presencesParCulte;
    }
    
    public function getPresencesParMois(): array
    {
        return $this->presencesParMois;
    }
    
    public function getTauxOccupationMoyen(): float
    {
        if ($this->totalCultes == 0) {
            return 0;
        }
        
        $capaciteTheorique = 200;
        return ($this->moyennePresencesParCulte / $capaciteTheorique) * 100;
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
    
    public function getMoisPlusFrequente(): ?array
    {
        if (empty($this->presencesParMois)) {
            return null;
        }
        
        $meilleur = null;
        foreach ($this->presencesParMois as $mois => $stat) {
            if ($meilleur === null || $stat['nombre_presences'] > $meilleur['nombre_presences']) {
                $meilleur = array_merge(['mois' => $mois], $stat);
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
            'total_presences_enregistrees' => $this->totalPresencesEnregistrees,
            'moyenne_presences_par_culte' => $this->moyennePresencesParCulte,
            'total_fideles_uniques' => $this->totalFidelesUniques,
            'top_fideles_presents' => $this->topFidelesPresent,
        ];
    }
    
    public function getResume(): string
    {
        if ($this->isEmpty()) {
            return 'Aucune donnée disponible';
        }
        
        return sprintf(
            '%d culte(s) | %d fidèle(s) | Moyenne: %d par culte | %d présences individuelles enregistrées',
            $this->totalCultes,
            $this->totalFideles,
            (int)$this->moyenneParCulte,
            $this->totalPresencesEnregistrees
        );
    }
}