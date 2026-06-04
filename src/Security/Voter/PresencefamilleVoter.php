<?php

namespace App\Security\Voter;

use App\Entity\Presencefamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencefamilleVoter extends Voter {

    public const PRESENCE_VIEW = 'seancefamille_presence';
    public const PRESENCE_DELETE = 'seancefamille_presence_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencefamille): bool {
        return in_array($attribute, [self::PRESENCE_VIEW, self::PRESENCE_DELETE]) 
            && $presencefamille instanceof Presencefamille;
    }

    protected function voteOnAttribute(string $attribute, $presencefamille, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        // Ces rôles voient TOUTES les présences de l'église
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $presencefamille, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les présences des familles de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $presencefamille, $user);
        }
        
        // ROLE_RESPONSABLE_FAMILLE : voit uniquement les présences de sa famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            return $this->canViewByCellule($attribute, $presencefamille, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les présences des familles de sa zone
     */
    private function canViewByZone(string $attribute, Presencefamille $presencefamille, User $user): bool {
        $famille = $presencefamille->getCellule();
        if (!$famille) {
            return false;
        }
        
        $zone = $famille->getZone();
        if (!$zone) {
            return false;
        }
        
        // Vérifier que la zone appartient au responsable
        $zoneResponsable = $zone->getUsers();
        if (!$zoneResponsable || $zoneResponsable !== $user) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $presencefamille, $user);
    }
    
    /**
     * Responsable de famille : voit uniquement sa famille
     */
    private function canViewByCellule(string $attribute, Presencefamille $presencefamille, User $user): bool {
        $famille = $presencefamille->getCellule();
        if (!$famille) {
            return false;
        }
        
        // Vérifier que l'utilisateur est responsable de cette famille
        // ou qu'il appartient à la famille (si plusieurs utilisateurs par famille)
        $familleResponsable = $famille->getUsers();
        
        if ($familleResponsable && $familleResponsable === $user) {
            return $this->checkAttribute($attribute, $presencefamille, $user);
        }
        
        // Vérifier si l'utilisateur appartient à la famille (pour les membres)
        if (method_exists($famille, 'getUsers') && $famille->getUsers()->contains($user)) {
            return $this->checkAttribute($attribute, $presencefamille, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie le type d'action (view, delete)
     */
    private function checkAttribute(string $attribute, Presencefamille $presencefamille, User $user): bool {
        switch ($attribute) {
            case self::PRESENCE_VIEW:
                return true;
                
            case self::PRESENCE_DELETE:
                return $this->canDelete($presencefamille, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer
     */
    private function canDelete(Presencefamille $presencefamille, User $user): bool {
        // Les rôles supérieurs peuvent tout supprimer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $famille = $presencefamille->getCellule();
        if (!$famille) {
            return false;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers() === $user) {
                return true;
            }
        }
        
        // Responsable de famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            if ($famille->getUsers() === $user) {
                return true;
            }
        }
        
        return false;
    }
}