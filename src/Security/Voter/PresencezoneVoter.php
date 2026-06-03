<?php

namespace App\Security\Voter;

use App\Entity\Presencezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencezoneVoter extends Voter {

    public const PRESENCE_VIEW = 'seancezone_presence';
    public const PRESENCE_DELETE = 'seancezone_presence_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencezone): bool {
        return in_array($attribute, [self::PRESENCE_VIEW, self::PRESENCE_DELETE]) 
            && $presencezone instanceof Presencezone;
    }

    protected function voteOnAttribute(string $attribute, $presencezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE voient TOUTES les présences
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $presencezone, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE voit uniquement les présences de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $presencezone, $user);
        }
        
        return false;
    }
    
    private function canViewByZone(string $attribute, Presencezone $presencezone, User $user): bool {
        $zone = $presencezone->getZone();
        if (!$zone) {
            return false;
        }
        
        $zoneResponsable = $zone->getUsers();
        if (!$zoneResponsable || $zoneResponsable !== $user) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $presencezone, $user);
    }
    
    private function checkAttribute(string $attribute, Presencezone $presencezone, User $user): bool {
        switch ($attribute) {
            case self::PRESENCE_VIEW:
                return true;
                
            case self::PRESENCE_DELETE:
                return $this->canDelete($presencezone, $user);
        }
        return false;
    }
    
    private function canDelete(Presencezone $presencezone, User $user): bool {
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $presencezone->getZone();
            if ($zone && $zone->getUsers() === $user) {
                return true;
            }
        }
        
        return false;
    }
}