<?php

namespace App\Security\Voter;

use App\Entity\Depensegroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DepensegroupeVoter extends Voter {

    public const DEPENSE_VIEW = 'depensegroupe_view';
    public const DEPENSE_EDIT = 'depensegroupe_edit';
    public const DEPENSE_DELETE = 'depensegroupe_delete';
    public const DEPENSE_CREATE = 'depensegroupe_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $depensegroupe): bool {
        return in_array($attribute, [
            self::DEPENSE_VIEW, 
            self::DEPENSE_EDIT, 
            self::DEPENSE_DELETE,
            self::DEPENSE_CREATE
        ]) && ($depensegroupe instanceof Depensegroupe || $depensegroupe === null);
    }

    protected function voteOnAttribute(string $attribute, $depensegroupe, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        // Ces rôles voient TOUTES les dépenses de l'église
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $depensegroupe, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les dépenses des groupes de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $depensegroupe, $user);
        }
        
        // ROLE_RESPONSABLE_GROUPE : voit uniquement les dépenses de sa groupe
        if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            return $this->canViewByGroupe($attribute, $depensegroupe, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les dépenses des groupes de sa zone
     */
    private function canViewByDepartement(string $attribute, ?Depensegroupe $depensegroupe, User $user): bool {
        $zone = $user->getDepartement();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de dépense spécifique)
        if ($depensegroupe === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $groupe = $depensegroupe->getGroupe();
        if (!$groupe) {
            return false;
        }
        
        $groupeDepartement = $groupe->getDepartement();
        if (!$groupeDepartement || $groupeDepartement->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensegroupe, $user);
    }
    
    /**
     * Responsable de groupe : voit uniquement sa groupe
     */
    private function canViewByGroupe(string $attribute, ?Depensegroupe $depensegroupe, User $user): bool {
        $groupeUser = $user->getGroupe();
        if (!$groupeUser) {
            return false;
        }
        
        // Pour la création (pas de dépense spécifique)
        if ($depensegroupe === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $groupe = $depensegroupe->getGroupe();
        if (!$groupe || $groupe->getId() !== $groupeUser->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensegroupe, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Depensegroupe $depensegroupe, User $user): bool {
        switch ($attribute) {
            case self::DEPENSE_VIEW:
                return true;
                
            case self::DEPENSE_CREATE:
                return $this->canCreate($depensegroupe, $user);
                
            case self::DEPENSE_EDIT:
                return $this->canEdit($depensegroupe, $user);
                
            case self::DEPENSE_DELETE:
                return $this->canDelete($depensegroupe, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une dépense
     */
    private function canCreate(?Depensegroupe $depensegroupe, User $user): bool {
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
        
        // Responsable de groupe
        if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une dépense
     */
    private function canEdit(Depensegroupe $depensegroupe, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $groupe = $depensegroupe->getGroupe();
        if (!$groupe) {
            return false;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $zone = $user->getDepartement();
            $groupeDepartement = $groupe->getDepartement();
            if ($zone && $groupeDepartement && $zone->getId() === $groupeDepartement->getId()) {
                return true;
            }
        }
        
        // Responsable de groupe
        if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            $groupeUser = $user->getGroupe();
            if ($groupeUser && $groupeUser->getId() === $groupe->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une dépense
     */
    private function canDelete(Depensegroupe $depensegroupe, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($depensegroupe, $user);
    }
}