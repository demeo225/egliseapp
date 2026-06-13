<?php

namespace App\DTO;

class BilanGroupeDTO
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
    private array $statistiquesParGroupe = [];
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
            $groupeNom = $seance->getGroupe() ? $seance->getGroupe()->getNom() : 'Non défini';
            if (!isset($this->statistiquesParGroupe[$groupeNom])) {
                $this->statistiquesParGroupe[$groupeNom] = [
                    'nombre_seances' => 0,
                    'total_participants' => 0,
                    'moyenne' => 0,
                    'total_offrandes' => 0
                ];
            }
            $this->statistiquesParGroupe[$groupeNom]['nombre_seances']++;
            $this->statistiquesParGroupe[$groupeNom]['total_participants'] += $totalSeance;
            $this->statistiquesParGroupe[$groupeNom]['total_offrandes'] += $offrande;
            
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
        foreach ($this->statistiquesParGroupe as &$stat) {
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
    public function getStatistiquesParGroupe(): array { return $this->statistiquesParGroupe; }
    public function getStatistiquesParMois(): array { return $this->statistiquesParMois; }
    
    public function getMeilleurGroupe(): ?array
    {
        if (empty($this->statistiquesParGroupe)) return null;
        
        $meilleur = null;
        foreach ($this->statistiquesParGroupe as $nom => $stat) {
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