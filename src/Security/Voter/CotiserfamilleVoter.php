<?php

namespace App\Security\Voter;

use App\Entity\Cotiserfamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotiserfamilleVoter extends Voter {

    public const COTISATIONFAMILLE_EDIT = 'cotiserfamille_edit';
    public const COTISATIONFAMILLE_VIEW = 'cotiserfamille_index';
    public const COTISATIONFAMILLE_DELETE = 'cotiserfamille_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotiserfamille): bool {
        return in_array($attribute, [self::COTISATIONFAMILLE_EDIT, self::COTISATIONFAMILLE_VIEW, self::COTISATIONFAMILLE_DELETE]) 
            && $cotiserfamille instanceof Cotiserfamille;
    }

    protected function voteOnAttribute(string $attribute, $cotiserfamille, TokenInterface $token): bool {
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
        $famille = $cotiserfamille->getFamille();
        if (null === $famille) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONFAMILLE_EDIT:
                return $this->canEdit($cotiserfamille, $user);
            case self::COTISATIONFAMILLE_VIEW:
                return $this->canView($cotiserfamille, $user);
            case self::COTISATIONFAMILLE_DELETE:
                return $this->canDelete($cotiserfamille, $user);
        }

        return false;
    }

    private function canEdit(Cotiserfamille $cotiserfamille, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $cotiserfamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la famille
        if ($famille->getUser() && $user === $famille->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient à la famille
        if (method_exists($famille, 'getUsers')) {
            return $famille->getUsers()->contains($user);
        }

        // Vérifier dans les membres de la famille
        if (method_exists($famille, 'getMembres')) {
            foreach ($famille->getMembres() as $membre) {
                if ($user === $membre) {
                    return true;
                }
            }
        }

        return false;
    }

    private function canView(Cotiserfamille $cotiserfamille, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $cotiserfamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la famille
        if ($famille->getUser() && $user === $famille->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient à la famille
        if (method_exists($famille, 'getUsers')) {
            return $famille->getUsers()->contains($user);
        }

        // Vérifier dans les membres de la famille
        if (method_exists($famille, 'getMembres')) {
            foreach ($famille->getMembres() as $membre) {
                if ($user === $membre) {
                    return true;
                }
            }
        }

        return false;
    }

    private function canDelete(Cotiserfamille $cotiserfamille, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $cotiserfamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Seul le responsable de la famille peut supprimer
        return $famille->getUser() && $user === $famille->getUser();
    }
}