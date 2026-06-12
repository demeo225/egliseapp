<?php

namespace App\Security\Voter;

use App\Entity\Invitezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitezoneVoter extends Voter {

    public const INVITE_VIEW = 'invitezone_view';
    public const INVITE_EDIT = 'invitezone_edit';
    public const INVITE_DELETE = 'invitezone_delete';
    public const INVITE_CREATE = 'invitezone_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitezone): bool {
        return in_array($attribute, [
            self::INVITE_VIEW, 
            self::INVITE_EDIT, 
            self::INVITE_DELETE,
            self::INVITE_CREATE
        ]) && ($invitezone instanceof Invitezone || $invitezone === null);
    }

    protected function voteOnAttribute(string $attribute, $invitezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $invitezone, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les invités des séances de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $invitezone, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les invités des séances de sa zone
     */
    private function canViewByZone(string $attribute, ?Invitezone $invitezone, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas d'invité spécifique)
        if ($invitezone === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $seancezone = $invitezone->getSeancezone();
        if (!$seancezone) {
            return false;
        }
        
        $seanceZone = $seancezone->getZone();
        if (!$seanceZone || $seanceZone->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $invitezone, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Invitezone $invitezone, User $user): bool {
        switch ($attribute) {
            case self::INVITE_VIEW:
                return true;
                
            case self::INVITE_CREATE:
                return $this->canCreate($invitezone, $user);
                
            case self::INVITE_EDIT:
                return $this->canEdit($invitezone, $user);
                
            case self::INVITE_DELETE:
                return $this->canDelete($invitezone, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer un invité
     */
    private function canCreate(?Invitezone $invitezone, User $user): bool {
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
     * Vérifie si l'utilisateur peut modifier un invité
     */
    private function canEdit(Invitezone $invitezone, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $seancezone = $invitezone->getSeancezone();
            if ($zone && $seancezone) {
                $seanceZone = $seancezone->getZone();
                if ($seanceZone && $seanceZone->getId() === $zone->getId()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer un invité
     */
    private function canDelete(Invitezone $invitezone, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($invitezone, $user);
    }
}