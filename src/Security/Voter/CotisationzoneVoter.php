<?php

namespace App\Security\Voter;

use App\Entity\Cotisationzone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationzoneVoter extends Voter {

    public const COTISATION_VIEW = 'cotisationzone_view';
    public const COTISATION_EDIT = 'cotisationzone_edit';
    public const COTISATION_DELETE = 'cotisationzone_delete';
    public const COTISATION_CREATE = 'cotisationzone_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationzone): bool {
        return in_array($attribute, [
            self::COTISATION_VIEW, 
            self::COTISATION_EDIT, 
            self::COTISATION_DELETE,
            self::COTISATION_CREATE
        ]) && ($cotisationzone instanceof Cotisationzone || $cotisationzone === null);
    }

    protected function voteOnAttribute(string $attribute, $cotisationzone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $cotisationzone, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les cotisations de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $cotisationzone, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les cotisations de sa zone
     */
    private function canViewByZone(string $attribute, ?Cotisationzone $cotisationzone, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de cotisation spécifique)
        if ($cotisationzone === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $cotisationZone = $cotisationzone->getZone();
        if (!$cotisationZone || $cotisationZone->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $cotisationzone, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Cotisationzone $cotisationzone, User $user): bool {
        switch ($attribute) {
            case self::COTISATION_VIEW:
                return true;
                
            case self::COTISATION_CREATE:
                return $this->canCreate($cotisationzone, $user);
                
            case self::COTISATION_EDIT:
                return $this->canEdit($cotisationzone, $user);
                
            case self::COTISATION_DELETE:
                return $this->canDelete($cotisationzone, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une cotisation
     */
    private function canCreate(?Cotisationzone $cotisationzone, User $user): bool {
        // Les rôles supérieurs peuvent créer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une cotisation
     */
    private function canEdit(Cotisationzone $cotisationzone, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $cotisationZone = $cotisationzone->getZone();
            if ($zone && $cotisationZone && $zone->getId() === $cotisationZone->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une cotisation
     */
    private function canDelete(Cotisationzone $cotisationzone, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($cotisationzone, $user);
    }
}