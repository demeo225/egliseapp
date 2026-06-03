<?php

namespace App\Security\Voter;

use App\Entity\Cotiserzone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotiserzoneVoter extends Voter {

    public const COTISATIONZONE_EDIT = 'cotiserzone_edit';
    public const COTISATIONZONE_VIEW = 'cotiserzone_index';
    public const COTISATIONZONE_DELETE = 'cotiserzone_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotiserzone): bool {
        return in_array($attribute, [self::COTISATIONZONE_EDIT, self::COTISATIONZONE_VIEW, self::COTISATIONZONE_DELETE]) 
            && $cotiserzone instanceof Cotiserzone;
    }

    protected function voteOnAttribute(string $attribute, $cotiserzone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin et secrétaire ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la zone existe
        $zone = $cotiserzone->getZone();
        if (null === $zone) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONZONE_EDIT:
                return $this->canEdit($cotiserzone, $user);
            case self::COTISATIONZONE_VIEW:
                return $this->canView($cotiserzone, $user);
            case self::COTISATIONZONE_DELETE:
                return $this->canDelete($cotiserzone, $user);
        }

        return false;
    }

    private function canEdit(Cotiserzone $cotiserzone, User $user): bool {
        $zone = $cotiserzone->getZone();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($zone->getUser() && $user === $zone->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient à la zone via les cellules
        return $this->isUserInZone($zone, $user);
    }

    private function canView(Cotiserzone $cotiserzone, User $user): bool {
        $zone = $cotiserzone->getZone();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($zone->getUser() && $user === $zone->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient à la zone
        return $this->isUserInZone($zone, $user);
    }

    private function canDelete(Cotiserzone $cotiserzone, User $user): bool {
        $zone = $cotiserzone->getZone();
        
        // Seul le responsable de zone peut supprimer
        return $zone->getUser() && $user === $zone->getUser();
    }

    /**
     * Vérifie si un utilisateur appartient à une zone
     */
    private function isUserInZone($zone, User $user): bool {
        // Si la zone a une collection d'utilisateurs directe
        if (method_exists($zone, 'getUsers')) {
            if ($zone->getUsers()->contains($user)) {
                return true;
            }
        }
        
        // Vérifier dans les cellules de la zone
        foreach ($zone->getCellules() as $cellule) {
            if ($cellule->getUsers()->contains($user)) {
                return true;
            }
        }
        
        return false;
    }
}