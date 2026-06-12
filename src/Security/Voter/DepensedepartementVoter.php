<?php

namespace App\Security\Voter;

use App\Entity\Depensezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DepensezoneVoter extends Voter {

    public const DEPENSEDEPARTEMENT_VIEW = 'depensezone_view';
    public const DEPENSEDEPARTEMENT_EDIT = 'depensezone_edit';
    public const DEPENSEDEPARTEMENT_DELETE = 'depensezone_delete';
    public const DEPENSEDEPARTEMENT_CREATE = 'depensezone_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $depensezone): bool {
        return in_array($attribute, [
            self::DEPENSEDEPARTEMENT_VIEW, 
            self::DEPENSEDEPARTEMENT_EDIT, 
            self::DEPENSEDEPARTEMENT_DELETE,
            self::DEPENSEDEPARTEMENT_CREATE
        ]) && ($depensezone instanceof Depensezone || $depensezone === null);
    }

    protected function voteOnAttribute(string $attribute, $depensezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $depensezone, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les depenses de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $depensezone, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les depenses de sa zone
     */
    private function canViewByDepartement(string $attribute, ?Depensezone $depensezone, User $user): bool {
        $zone = $user->getDepartement();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de depense spécifique)
        if ($depensezone === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $depenseDepartement = $depensezone->getDepartement();
        if (!$depenseDepartement || $depenseDepartement->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensezone, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Depensezone $depensezone, User $user): bool {
        switch ($attribute) {
            case self::DEPENSEDEPARTEMENT_VIEW:
                return true;
                
            case self::DEPENSEDEPARTEMENT_CREATE:
                return $this->canCreate($depensezone, $user);
                
            case self::DEPENSEDEPARTEMENT_EDIT:
                return $this->canEdit($depensezone, $user);
                
            case self::DEPENSEDEPARTEMENT_DELETE:
                return $this->canDelete($depensezone, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une depense
     */
    private function canCreate(?Depensezone $depensezone, User $user): bool {
        // Les rôles supérieurs peuvent créer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une depense
     */
    private function canEdit(Depensezone $depensezone, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $zone = $user->getDepartement();
            $depenseDepartement = $depensezone->getDepartement();
            if ($zone && $depenseDepartement && $zone->getId() === $depenseDepartement->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une depense
     */
    private function canDelete(Depensezone $depensezone, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($depensezone, $user);
    }
}