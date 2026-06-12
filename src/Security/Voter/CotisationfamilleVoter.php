<?php

namespace App\Security\Voter;

use App\Entity\Cotisationfamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationfamilleVoter extends Voter {

    public const COTISATION_VIEW = 'cotisationfamille_view';
    public const COTISATION_EDIT = 'cotisationfamille_edit';
    public const COTISATION_DELETE = 'cotisationfamille_delete';
    public const COTISATION_CREATE = 'cotisationfamille_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationfamille): bool {
        return in_array($attribute, [
            self::COTISATION_VIEW, 
            self::COTISATION_EDIT, 
            self::COTISATION_DELETE,
            self::COTISATION_CREATE
        ]) && ($cotisationfamille instanceof Cotisationfamille || $cotisationfamille === null);
    }

    protected function voteOnAttribute(string $attribute, $cotisationfamille, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        // Ces rôles voient TOUTES les cotisations de l'église
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $cotisationfamille, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les cotisations des familles de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $cotisationfamille, $user);
        }
        
        // ROLE_RESPONSABLE_FAMILLE : voit uniquement les cotisations de sa famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            return $this->canViewByFamille($attribute, $cotisationfamille, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les cotisations des familles de sa zone
     */
    private function canViewByZone(string $attribute, ?Cotisationfamille $cotisationfamille, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de cotisation spécifique)
        if ($cotisationfamille === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $famille = $cotisationfamille->getFamille();
        if (!$famille) {
            return false;
        }
        
        $familleZone = $famille->getZone();
        if (!$familleZone || $familleZone->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $cotisationfamille, $user);
    }
    
    /**
     * Responsable de famille : voit uniquement sa famille
     */
    private function canViewByFamille(string $attribute, ?Cotisationfamille $cotisationfamille, User $user): bool {
        $familleUser = $user->getFamille();
        if (!$familleUser) {
            return false;
        }
        
        // Pour la création (pas de cotisation spécifique)
        if ($cotisationfamille === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $famille = $cotisationfamille->getFamille();
        if (!$famille || $famille->getId() !== $familleUser->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $cotisationfamille, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Cotisationfamille $cotisationfamille, User $user): bool {
        switch ($attribute) {
            case self::COTISATION_VIEW:
                return true;
                
            case self::COTISATION_CREATE:
                return $this->canCreate($cotisationfamille, $user);
                
            case self::COTISATION_EDIT:
                return $this->canEdit($cotisationfamille, $user);
                
            case self::COTISATION_DELETE:
                return $this->canDelete($cotisationfamille, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une cotisation
     */
    private function canCreate(?Cotisationfamille $cotisationfamille, User $user): bool {
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
        
        // Responsable de famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une cotisation
     */
    private function canEdit(Cotisationfamille $cotisationfamille, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $famille = $cotisationfamille->getFamille();
        if (!$famille) {
            return false;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $familleZone = $famille->getZone();
            if ($zone && $familleZone && $zone->getId() === $familleZone->getId()) {
                return true;
            }
        }
        
        // Responsable de famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            $familleUser = $user->getFamille();
            if ($familleUser && $familleUser->getId() === $famille->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une cotisation
     */
    private function canDelete(Cotisationfamille $cotisationfamille, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($cotisationfamille, $user);
    }
}