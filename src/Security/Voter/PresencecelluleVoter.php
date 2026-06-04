<?php

namespace App\Security\Voter;

use App\Entity\Presencecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencecelluleVoter extends Voter {

    public const PRESENCE_VIEW = 'seancecellule_presence';
    public const PRESENCE_DELETE = 'seancecellule_presence_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencecellule): bool {
        return in_array($attribute, [self::PRESENCE_VIEW, self::PRESENCE_DELETE]) 
            && $presencecellule instanceof Presencecellule;
    }

    protected function voteOnAttribute(string $attribute, $presencecellule, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        // Ces rôles voient TOUTES les présences de l'église
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $presencecellule, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les présences des cellules de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $presencecellule, $user);
        }
        
        // ROLE_RESPONSABLE_CELLULE : voit uniquement les présences de sa cellule
        if ($this->security->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            return $this->canViewByCellule($attribute, $presencecellule, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les présences des cellules de sa zone
     */
    private function canViewByZone(string $attribute, Presencecellule $presencecellule, User $user): bool {
        $cellule = $presencecellule->getCellule();
        if (!$cellule) {
            return false;
        }
        
        $zone = $cellule->getZone();
        if (!$zone) {
            return false;
        }
        
        // Vérifier que la zone appartient au responsable
        $zoneResponsable = $zone->getUsers();
        if (!$zoneResponsable || $zoneResponsable !== $user) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $presencecellule, $user);
    }
    
    /**
     * Responsable de cellule : voit uniquement sa cellule
     */
    private function canViewByCellule(string $attribute, Presencecellule $presencecellule, User $user): bool {
        $cellule = $presencecellule->getCellule();
        if (!$cellule) {
            return false;
        }
        
        // Vérifier que l'utilisateur est responsable de cette cellule
        // ou qu'il appartient à la cellule (si plusieurs utilisateurs par cellule)
        $celluleResponsable = $cellule->getUsers();
        
        if ($celluleResponsable && $celluleResponsable === $user) {
            return $this->checkAttribute($attribute, $presencecellule, $user);
        }
        
        // Vérifier si l'utilisateur appartient à la cellule (pour les membres)
        if (method_exists($cellule, 'getUsers') && $cellule->getUsers()->contains($user)) {
            return $this->checkAttribute($attribute, $presencecellule, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie le type d'action (view, delete)
     */
    private function checkAttribute(string $attribute, Presencecellule $presencecellule, User $user): bool {
        switch ($attribute) {
            case self::PRESENCE_VIEW:
                return true;
                
            case self::PRESENCE_DELETE:
                return $this->canDelete($presencecellule, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer
     */
    private function canDelete(Presencecellule $presencecellule, User $user): bool {
        // Les rôles supérieurs peuvent tout supprimer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $cellule = $presencecellule->getCellule();
        if (!$cellule) {
            return false;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUsers() === $user) {
                return true;
            }
        }
        
        // Responsable de cellule
        if ($this->security->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            if ($cellule->getUsers() === $user) {
                return true;
            }
        }
        
        return false;
    }
}