<?php

namespace App\DTO;

class BilanDepartementDTO
{
    private array $seances = [];
    private int $totalSeances = 0;
    private int $totalParticipants = 0;
    private float $moyenneParSeance = 0;
    private int $totalHommes = 0;
    private int $totalFemmes = 0;
    private int $totalEnfants = 0;
    private int $totalInvites = 0;
    private int $totalOffrandes = 0;
    private array $statistiquesParDepartement = [];
    private array $statistiquesParMois = [];

    public function __construct(?array $seances = null)
    {
        if ($seances !== null) {
            $this->seances = $seances;
            $this->calculerStatistiques();
        }
    }

    private function calculerStatistiques(): void
    {
        $this->totalSeances = count($this->seances);
        
        foreach ($this->seances as $seance) {
            // Calcul des totaux par catégorie
            $hommes = $seance->getNbrepresent() ?? 0;
            $femmes = $seance->getFemme() ?? 0;
            $enfants = $seance->getEnfant() ?? 0;
          //  $invites = $seance->getTotalInvites() ?? 0;
            $offrande = $seance->getOffrande() ?? 0;
            
            $this->totalHommes += $hommes;
            $this->totalFemmes += $femmes;
            $this->totalEnfants += $enfants;
          //  $this->totalInvites += $invites;
            $this->totalOffrandes += $offrande;
            
            $totalSeance = $hommes + $femmes + $enfants ;
            $this->totalParticipants += $totalSeance;
            
            // Statistiques par département
            $departementNom = $seance->getDepartement() ? $seance->getDepartement()->getNom() : 'Non défini';
            if (!isset($this->statistiquesParDepartement[$departementNom])) {
                $this->statistiquesParDepartement[$departementNom] = [
                    'nombre_seances' => 0,
                    'total_participants' => 0,
                    'moyenne' => 0,
                    'total_offrandes' => 0
                ];
            }
            $this->statistiquesParDepartement[$departementNom]['nombre_seances']++;
            $this->statistiquesParDepartement[$departementNom]['total_participants'] += $totalSeance;
            $this->statistiquesParDepartement[$departementNom]['total_offrandes'] += $offrande;
            
            // Statistiques par mois
            if ($seance->getDatesuper()) {
                $mois = $seance->getDatesuper()->format('Y-m');
                $moisLibelle = $seance->getDatesuper()->format('F Y');
                if (!isset($this->statistiquesParMois[$mois])) {
                    $this->statistiquesParMois[$mois] = [
                        'libelle' => $moisLibelle,
                        'nombre_seances' => 0,
                        'total_participants' => 0,
                        'total_offrandes' => 0,
                        'moyenne' => 0
                    ];
                }
                $this->statistiquesParMois[$mois]['nombre_seances']++;
                $this->statistiquesParMois[$mois]['total_participants'] += $totalSeance;
                $this->statistiquesParMois[$mois]['total_offrandes'] += $offrande;
            }
        }
        
        // Calcul des moyennes par département
        foreach ($this->statistiquesParDepartement as &$stat) {
            if ($stat['nombre_seances'] > 0) {
                $stat['moyenne'] = $stat['total_participants'] / $stat['nombre_seances'];
            }
        }
        
        // Calcul des moyennes par mois
        foreach ($this->statistiquesParMois as &$stat) {
            if ($stat['nombre_seances'] > 0) {
                $stat['moyenne'] = $stat['total_participants'] / $stat['nombre_seances'];
            }
        }
        
        // Calcul de la moyenne générale
        if ($this->totalSeances > 0) {
            $this->moyenneParSeance = $this->totalParticipants / $this->totalSeances;
        }
    }

    // Getters
    public function getSeances(): array { return $this->seances; }
    public function getTotalSeances(): int { return $this->totalSeances; }
    public function getTotalParticipants(): int { return $this->totalParticipants; }
    public function getMoyenneParSeance(): float { return $this->moyenneParSeance; }
    public function getTotalHommes(): int { return $this->totalHommes; }
    public function getTotalFemmes(): int { return $this->totalFemmes; }
    public function getTotalEnfants(): int { return $this->totalEnfants; }
    public function getTotalInvites(): int { return $this->totalInvites; }
    public function getTotalOffrandes(): int { return $this->totalOffrandes; }
    
    public function isEmpty(): bool { return empty($this->seances); }
    public function getStatistiquesParDepartement(): array { return $this->statistiquesParDepartement; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
    
    public function getMeilleurDepartement(): ?array
    {
        if (empty($this->statistiquesParDepartement)) return null;
        
        $meilleur = null;
        foreach ($this->statistiquesParDepartement as $nom => $stat) {
            if ($meilleur === null || $stat['total_participants'] > $meilleur['total_participants']) {
                $meilleur = array_merge(['nom' => $nom], $stat);
            }
        }
        return $meilleur;
    }
    
    public function getResume(): string
    {
        if ($this->isEmpty()) {
            return 'Aucune activité disponible';
        }
        
        return sprintf(
            '%d séance(s) | %d participant(s) | Moyenne: %d par séance | Total offrandes: %d FCFA',
            $this->totalSeances,
            $this->totalParticipants,
            (int)$this->moyenneParSeance,
            $this->totalOffrandes
        );
    }
}