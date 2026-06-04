<?php

namespace App\Security\Voter;

use App\Entity\Cotisationfamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationfamilleVoter extends Voter {

    public const COTISATIONFAMILLE_EDIT = 'cotisationfamille_edit';
    public const COTISATIONFAMILLE_VIEW = 'cotisationfamille_index';
    public const COTISATIONFAMILLE_DELETE = 'cotisationfamille_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationfamille): bool {
        return in_array($attribute, [self::COTISATIONFAMILLE_EDIT, self::COTISATIONFAMILLE_VIEW, self::COTISATIONFAMILLE_DELETE]) 
            && $cotisationfamille instanceof Cotisationfamille;
    }

    protected function voteOnAttribute(string $attribute, $cotisationfamille, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la famille existe
        $famille = $cotisationfamille->getFamille();
        if (null === $famille) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONFAMILLE_EDIT:
                return $this->canEdit($cotisationfamille, $user);
            case self::COTISATIONFAMILLE_VIEW:
                return $this->canView($cotisationfamille, $user);
            case self::COTISATIONFAMILLE_DELETE:
                return $this->canDelete($cotisationfamille, $user);
        }

        return false;
    }

    private function canEdit(Cotisationfamille $cotisationfamille, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $cotisationfamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la famille
        return $famille->getUsers()->contains($user);
    }

    private function canView(Cotisationfamille $cotisationfamille, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $cotisationfamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la famille
        return $famille->getUsers()->contains($user);
    }

    private function canDelete(Cotisationfamille $cotisationfamille, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $cotisationfamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la famille
        return $famille->getUsers()->contains($user);
    }
}