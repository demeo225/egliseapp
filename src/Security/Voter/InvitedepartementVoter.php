<?php

namespace App\Security\Voter;

use App\Entity\Invitedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitedepartementVoter extends Voter {

    public const INVITE_VIEW = 'invitedepartement_view';
    public const INVITE_EDIT = 'invitedepartement_edit';
    public const INVITE_DELETE = 'invitedepartement_delete';
    public const INVITE_CREATE = 'invitedepartement_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitedepartement): bool {
        return in_array($attribute, [
            self::INVITE_VIEW, 
            self::INVITE_EDIT, 
            self::INVITE_DELETE,
            self::INVITE_CREATE
        ]) && ($invitedepartement instanceof Invitedepartement || $invitedepartement === null);
    }

    protected function voteOnAttribute(string $attribute, $invitedepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $invitedepartement, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les invités des séances de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $invitedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de departement : voit les invités des séances de sa departement
     */
    private function canViewByDepartement(string $attribute, ?Invitedepartement $invitedepartement, User $user): bool {
        $departement = $user->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Pour la création (pas d'invité spécifique)
        if ($invitedepartement === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $seancedepartement = $invitedepartement->getSeancedepartement();
        if (!$seancedepartement) {
            return false;
        }
        
        $seanceDepartement = $seancedepartement->getDepartement();
        if (!$seanceDepartement || $seanceDepartement->getId() !== $departement->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $invitedepartement, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Invitedepartement $invitedepartement, User $user): bool {
        switch ($attribute) {
            case self::INVITE_VIEW:
                return true;
                
            case self::INVITE_CREATE:
                return $this->canCreate($invitedepartement, $user);
                
            case self::INVITE_EDIT:
                return $this->canEdit($invitedepartement, $user);
                
            case self::INVITE_DELETE:
                return $this->canDelete($invitedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer un invité
     */
    private function canCreate(?Invitedepartement $invitedepartement, User $user): bool {
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
     * Vérifie si l'utilisateur peut modifier un invité
     */
    private function canEdit(Invitedepartement $invitedepartement, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $user->getDepartement();
            $seancedepartement = $invitedepartement->getSeancedepartement();
            if ($departement && $seancedepartement) {
                $seanceDepartement = $seancedepartement->getDepartement();
                if ($seanceDepartement && $seanceDepartement->getId() === $departement->getId()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer un invité
     */
    private function canDelete(Invitedepartement $invitedepartement, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($invitedepartement, $user);
    }
}