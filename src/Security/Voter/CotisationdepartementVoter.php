<?php

namespace App\Security\Voter;

use App\Entity\Cotisationdepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationdepartementVoter extends Voter {

    public const COTISATION_VIEW = 'cotisationdepartement_view';
    public const COTISATION_EDIT = 'cotisationdepartement_edit';
    public const COTISATION_DELETE = 'cotisationdepartement_delete';
    public const COTISATION_CREATE = 'cotisationdepartement_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationdepartement): bool {
        return in_array($attribute, [
            self::COTISATION_VIEW, 
            self::COTISATION_EDIT, 
            self::COTISATION_DELETE,
            self::COTISATION_CREATE
        ]) && ($cotisationdepartement instanceof Cotisationdepartement || $cotisationdepartement === null);
    }

    protected function voteOnAttribute(string $attribute, $cotisationdepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $cotisationdepartement, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les cotisations de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $cotisationdepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de departement : voit les cotisations de sa departement
     */
    private function canViewByDepartement(string $attribute, ?Cotisationdepartement $cotisationdepartement, User $user): bool {
        $departement = $user->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Pour la création (pas de cotisation spécifique)
        if ($cotisationdepartement === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $cotisationDepartement = $cotisationdepartement->getDepartement();
        if (!$cotisationDepartement || $cotisationDepartement->getId() !== $departement->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $cotisationdepartement, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Cotisationdepartement $cotisationdepartement, User $user): bool {
        switch ($attribute) {
            case self::COTISATION_VIEW:
                return true;
                
            case self::COTISATION_CREATE:
                return $this->canCreate($cotisationdepartement, $user);
                
            case self::COTISATION_EDIT:
                return $this->canEdit($cotisationdepartement, $user);
                
            case self::COTISATION_DELETE:
                return $this->canDelete($cotisationdepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une cotisation
     */
    private function canCreate(?Cotisationdepartement $cotisationdepartement, User $user): bool {
        // Les rôles supérieurs peuvent créer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une cotisation
     */
    private function canEdit(Cotisationdepartement $cotisationdepartement, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $user->getDepartement();
            $cotisationDepartement = $cotisationdepartement->getDepartement();
            if ($departement && $cotisationDepartement && $departement->getId() === $cotisationDepartement->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une cotisation
     */
    private function canDelete(Cotisationdepartement $cotisationdepartement, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($cotisationdepartement, $user);
    }
}