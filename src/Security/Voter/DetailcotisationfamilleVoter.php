<?php

namespace App\Security\Voter;

use App\Entity\Detailcotisationfamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationfamilleVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONFAMILLE_VIEW = 'cotiserfamille_detailfamille';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detailfamille): bool {
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONFAMILLE_VIEW]) 
            && $detailfamille instanceof Detailcotisationfamille;
    }

    protected function voteOnAttribute(string $attribute, $detailfamille, TokenInterface $token): bool {
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

        // Vérifier si la cotisation famille existe
        $cotisationfamille = $detailfamille->getCotisationfamille();
        if (null === $cotisationfamille) {
            return false;
        }
        
        // Vérifier si la famille existe
        $famille = $cotisationfamille->getFamille();
        if (null === $famille) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($detailfamille, $user);
            case self::DETAILCOTISATIONFAMILLE_VIEW:
                return $this->canViewDetail($detailfamille, $user);
        }

        return false;
    }

    private function canEdit(Detailcotisationfamille $detailfamille, User $user): bool {
        $cotisationfamille = $detailfamille->getCotisationfamille();
        $famille = $cotisationfamille->getFamille();
        
        // Responsable de zone
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

        // Vérifier si l'utilisateur appartient à la famille (si c'est une collection)
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

    private function canViewDetail(Detailcotisationfamille $detailfamille, User $user): bool {
        $cotisationfamille = $detailfamille->getCotisationfamille();
        $famille = $cotisationfamille->getFamille();
        
        // Responsable de zone
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
}