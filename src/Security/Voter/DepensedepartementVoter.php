<?php

namespace App\Security\Voter;

use App\Entity\Depensedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DepensedepartementVoter extends Voter {

    public const DEPENSEDEPARTEMENT_VIEW = 'depensedepartement_view';
    public const DEPENSEDEPARTEMENT_EDIT = 'depensedepartement_edit';
    public const DEPENSEDEPARTEMENT_DELETE = 'depensedepartement_delete';
    public const DEPENSEDEPARTEMENT_CREATE = 'depensedepartement_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $depensedepartement): bool {
        return in_array($attribute, [
            self::DEPENSEDEPARTEMENT_VIEW, 
            self::DEPENSEDEPARTEMENT_EDIT, 
            self::DEPENSEDEPARTEMENT_DELETE,
            self::DEPENSEDEPARTEMENT_CREATE
        ]) && ($depensedepartement instanceof Depensedepartement || $depensedepartement === null);
    }

    protected function voteOnAttribute(string $attribute, $depensedepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $depensedepartement, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les depenses de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $depensedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de departement : voit les depenses de sa departement
     */
    private function canViewByDepartement(string $attribute, ?Depensedepartement $depensedepartement, User $user): bool {
        $departement = $user->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Pour la création (pas de depense spécifique)
        if ($depensedepartement === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $depenseDepartement = $depensedepartement->getDepartement();
        if (!$depenseDepartement || $depenseDepartement->getId() !== $departement->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensedepartement, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Depensedepartement $depensedepartement, User $user): bool {
        switch ($attribute) {
            case self::DEPENSEDEPARTEMENT_VIEW:
                return true;
                
            case self::DEPENSEDEPARTEMENT_CREATE:
                return $this->canCreate($depensedepartement, $user);
                
            case self::DEPENSEDEPARTEMENT_EDIT:
                return $this->canEdit($depensedepartement, $user);
                
            case self::DEPENSEDEPARTEMENT_DELETE:
                return $this->canDelete($depensedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une depense
     */
    private function canCreate(?Depensedepartement $depensedepartement, User $user): bool {
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
     * Vérifie si l'utilisateur peut modifier une depense
     */
    private function canEdit(Depensedepartement $depensedepartement, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $user->getDepartement();
            $depenseDepartement = $depensedepartement->getDepartement();
            if ($departement && $depenseDepartement && $departement->getId() === $depenseDepartement->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une depense
     */
    private function canDelete(Depensedepartement $depensedepartement, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($depensedepartement, $user);
    }
}