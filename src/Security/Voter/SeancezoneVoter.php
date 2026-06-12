<?php

namespace App\Security\Voter;

use App\Entity\Seancezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancezoneVoter extends Voter {

    public const SEANCE_VIEW = 'seancezone_view';
    public const SEANCE_EDIT = 'seancezone_edit';
    public const SEANCE_DELETE = 'seancezone_delete';
    public const SEANCE_CREATE = 'seancezone_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancezone): bool {
        return in_array($attribute, [
            self::SEANCE_VIEW, 
            self::SEANCE_EDIT, 
            self::SEANCE_DELETE,
            self::SEANCE_CREATE
        ]) && ($seancezone instanceof Seancezone || $seancezone === null);
    }

    protected function voteOnAttribute(string $attribute, $seancezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $seancezone, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les séances de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $seancezone, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les séances de sa zone
     */
    private function canViewByZone(string $attribute, ?Seancezone $seancezone, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de séance spécifique)
        if ($seancezone === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $seanceZone = $seancezone->getZone();
        if (!$seanceZone || $seanceZone->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $seancezone, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Seancezone $seancezone, User $user): bool {
        switch ($attribute) {
            case self::SEANCE_VIEW:
                return true;
                
            case self::SEANCE_CREATE:
                return $this->canCreate($seancezone, $user);
                
            case self::SEANCE_EDIT:
                return $this->canEdit($seancezone, $user);
                
            case self::SEANCE_DELETE:
                return $this->canDelete($seancezone, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une séance
     */
    private function canCreate(?Seancezone $seancezone, User $user): bool {
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
     * Vérifie si l'utilisateur peut modifier une séance
     */
    private function canEdit(Seancezone $seancezone, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $seanceZone = $seancezone->getZone();
            if ($zone && $seanceZone && $zone->getId() === $seanceZone->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une séance
     */
    private function canDelete(Seancezone $seancezone, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($seancezone, $user);
    }
}