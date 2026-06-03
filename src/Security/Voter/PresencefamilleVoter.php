<?php

namespace App\Security\Voter;

use App\Entity\Presencefamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencefamilleVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const PRESENCEFAMILLE_VIEW = 'seancefamille_presence';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencefamille): bool {
        return in_array($attribute, [self::EDIT, self::PRESENCEFAMILLE_VIEW]) 
            && $presencefamille instanceof Presencefamille;
    }

    protected function voteOnAttribute(string $attribute, $presencefamille, TokenInterface $token): bool {
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

        // Vérifier si la famille existe
        $famille = $presencefamille->getFamille();
        if (null === $famille) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($presencefamille, $user);
            case self::PRESENCEFAMILLE_VIEW:
                return $this->canViewPresence($presencefamille, $user);
        }

        return false;
    }

    private function canEdit(Presencefamille $presencefamille, User $user): bool {
        $famille = $presencefamille->getFamille();
        
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

        return false;
    }

    private function canViewPresence(Presencefamille $presencefamille, User $user): bool {
        $famille = $presencefamille->getFamille();
        
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

        // Vérifier dans les membres de la famille (si la relation existe)
        foreach ($famille->getMembres() as $membre) {
            if ($user === $membre) {
                return true;
            }
        }

        return false;
    }
}