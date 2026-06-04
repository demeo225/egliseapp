<?php

namespace App\Security\Voter;

use App\Entity\Cotisationzone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationzoneVoter extends Voter {

    public const COTISATIONZONE_EDIT = 'cotisationzone_edit';
    public const COTISATIONZONE_VIEW = 'cotisationzone_index';
    public const COTISATIONZONE_DELETE = 'cotisationzone_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationzone): bool {
        return in_array($attribute, [self::COTISATIONZONE_EDIT, self::COTISATIONZONE_VIEW, self::COTISATIONZONE_DELETE]) 
            && $cotisationzone instanceof Cotisationzone;
    }

    protected function voteOnAttribute(string $attribute, $cotisationzone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la zone existe
        $zone = $cotisationzone->getZone();
        if (null === $zone) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONZONE_EDIT:
                return $this->canEdit($cotisationzone, $user);
            case self::COTISATIONZONE_VIEW:
                return $this->canView($cotisationzone, $user);
            case self::COTISATIONZONE_DELETE:
                return $this->canDelete($cotisationzone, $user);
        }

        return false;
    }

    private function canEdit(Cotisationzone $cotisationzone, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $zone = $cotisationzone->getZone();
        
        // Vérifier si l'utilisateur est responsable de zone
        // Si vous avez un rôle spécifique pour les responsables de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            // Si la zone a un responsable spécifique
            if ($zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la zone
        return $zone->getUsers()->contains($user);
    }

    private function canView(Cotisationzone $cotisationzone, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $zone = $cotisationzone->getZone();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            if ($zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la zone
        return $zone->getUsers()->contains($user);
    }

    private function canDelete(Cotisationzone $cotisationzone, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $zone = $cotisationzone->getZone();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            if ($zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la zone
        return $zone->getUsers()->contains($user);
    }
}