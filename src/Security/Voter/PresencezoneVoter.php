<?php

namespace App\Security\Voter;

use App\Entity\Presencezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencezoneVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const PRESENCEFAMILLE_VIEW = 'seancezone_presence';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencezone): bool {
        return in_array($attribute, [self::EDIT, self::PRESENCEFAMILLE_VIEW]) 
            && $presencezone instanceof Presencezone;
    }

    protected function voteOnAttribute(string $attribute, $presencezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_SECRETAIRE a tous les droits
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // ROLE_ADMIN a tous les droits (ajouté pour cohérence)
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la zone existe
        $zone = $presencezone->getZone();
        if (null === $zone) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($presencezone, $user);
            case self::PRESENCEFAMILLE_VIEW:
                return $this->canViewPresence($presencezone, $user);
        }

        return false;
    }

    private function canEdit(Presencezone $presencezone, User $user): bool {
        $zone = $presencezone->getZone();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($zone->getUser() && $user === $zone->getUser()) {
            return true;
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

    private function canViewPresence(Presencezone $presencezone, User $user): bool {
        $zone = $presencezone->getZone();
        
        // Le responsable de zone peut voir les présences
        if ($zone->getUser() && $user === $zone->getUser()) {
            return true;
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
}