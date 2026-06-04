<?php

namespace App\Security\Voter;

use App\Entity\Seancezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancezoneVoter extends Voter {

    public const SEANCEZONE_EDIT = 'seancezone_edit';
    public const SEANCEZONE_VIEW = 'seancezone_index';
    public const SEANCEZONE_DELETE = 'seancezone_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancezone): bool {
        return in_array($attribute, [self::SEANCEZONE_EDIT, self::SEANCEZONE_VIEW, self::SEANCEZONE_DELETE]) 
            && $seancezone instanceof Seancezone;
    }

    protected function voteOnAttribute(string $attribute, $seancezone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Le secrétaire a tous les droits sur les séances de zone
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la zone existe
        $zone = $seancezone->getZone();
        if (null === $zone) {
            return false;
        }

        switch ($attribute) {
            case self::SEANCEZONE_EDIT:
                return $this->canEdit($seancezone, $user);
            case self::SEANCEZONE_VIEW:
                return $this->canView($seancezone, $user);
            case self::SEANCEZONE_DELETE:
                return $this->canDelete($seancezone, $user);
        }

        return false;
    }

    private function canEdit(Seancezone $seancezone, User $user): bool {
        $zone = $seancezone->getZone();
        
        // Le responsable de zone peut modifier
        if ($zone->getUsers() && $user === $zone->getUsers()) {
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

    private function canView(Seancezone $seancezone, User $user): bool {
        $zone = $seancezone->getZone();
        
        // Le responsable de zone peut voir
        if ($zone->getUsers() && $user === $zone->getUsers()) {
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

    private function canDelete(Seancezone $seancezone, User $user): bool {
        $zone = $seancezone->getZone();
        
        // Seul le responsable de zone peut supprimer
        // (ou admin/secrétaire déjà géré plus haut)
        return $zone->getUsers() && $user === $zone->getUsers();
    }
}