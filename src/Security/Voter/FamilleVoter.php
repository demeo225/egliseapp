<?php

namespace App\Security\Voter;

use App\Entity\Famille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FamilleVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Famille;
    }

    protected function voteOnAttribute(string $attribute, $famille, TokenInterface $token): bool
    {
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

        // Vérifier si la famille existe
        if (null === $famille) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($famille, $user);
            case self::VIEW:
                return $this->canView($famille, $user);
        }

        return false;
    }

    private function canEdit(Famille $famille, User $user): bool
    {
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la famille
        if ($famille->getUsers() && $user === $famille->getUsers()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient à la famille (membres)
        if (method_exists($famille, 'getMembres')) {
            foreach ($famille->getMembres() as $membre) {
                if ($user === $membre) {
                    return true;
                }
            }
        }

        // Si la famille a une collection d'utilisateurs
        if (method_exists($famille, 'getUsers')) {
            return $famille->getUsers()->contains($user);
        }

        return false;
    }

    private function canView(Famille $famille, User $user): bool
    {
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la famille
        if ($famille->getUsers() && $user === $famille->getUsers()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient à la famille (membres)
        if (method_exists($famille, 'getMembres')) {
            foreach ($famille->getMembres() as $membre) {
                if ($user === $membre) {
                    return true;
                }
            }
        }

        // Si la famille a une collection d'utilisateurs
        if (method_exists($famille, 'getUsers')) {
            return $famille->getUsers()->contains($user);
        }

        return false;
    }
}