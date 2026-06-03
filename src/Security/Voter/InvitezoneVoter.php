<?php

namespace App\Security\Voter;

use App\Entity\Invitezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitezoneVoter extends Voter {

    public const SEANCEZONE_EDIT = 'invitezone_edit';
    public const SEANCEZONE_VIEW = 'invitezone_index';
    public const SEANCEZONE_DELETE = 'invitezone_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitezone): bool {
        return in_array($attribute, [self::SEANCEZONE_EDIT, self::SEANCEZONE_VIEW, self::SEANCEZONE_DELETE]) 
            && $invitezone instanceof Invitezone;
    }

    protected function voteOnAttribute(string $attribute, $invitezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        // ROLE_SECRETAIRE a tous les droits
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la séance de zone existe
        $seancezone = $invitezone->getSeancezone();
        if (null === $seancezone) {
            return false;
        }
        
        // Vérifier si la zone existe
        $zone = $seancezone->getZone();
        if (null === $zone) {
            return false;
        }

        switch ($attribute) {
            case self::SEANCEZONE_EDIT:
                return $this->canEdit($invitezone, $user);
            case self::SEANCEZONE_VIEW:
                return $this->canView($invitezone, $user);
            case self::SEANCEZONE_DELETE:
                return $this->canDelete($invitezone, $user);
        }

        return false;
    }

    private function canEdit(Invitezone $invitezone, User $user): bool {
        $zone = $invitezone->getSeancezone()->getZone();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $zone->getUser() && $user === $zone->getUser();
        }
        
        // Vérifier si l'utilisateur appartient à la zone (via les cellules)
        foreach ($zone->getCellules() as $cellule) {
            if ($cellule->getUsers()->contains($user)) {
                return true;
            }
        }
        
        // Si la zone a une collection d'utilisateurs directe
        if (method_exists($zone, 'getUsers')) {
            return $zone->getUsers()->contains($user);
        }
        
        return false;
    }

    private function canView(Invitezone $invitezone, User $user): bool {
        $zone = $invitezone->getSeancezone()->getZone();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $zone->getUser() && $user === $zone->getUser();
        }
        
        // Vérifier si l'utilisateur appartient à la zone
        foreach ($zone->getCellules() as $cellule) {
            if ($cellule->getUsers()->contains($user)) {
                return true;
            }
        }
        
        if (method_exists($zone, 'getUsers')) {
            return $zone->getUsers()->contains($user);
        }
        
        return false;
    }

    private function canDelete(Invitezone $invitezone, User $user): bool {
        $zone = $invitezone->getSeancezone()->getZone();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $zone->getUser() && $user === $zone->getUser();
        }
        
        // Vérifier si l'utilisateur appartient à la zone
        foreach ($zone->getCellules() as $cellule) {
            if ($cellule->getUsers()->contains($user)) {
                return true;
            }
        }
        
        if (method_exists($zone, 'getUsers')) {
            return $zone->getUsers()->contains($user);
        }
        
        return false;
    }
}