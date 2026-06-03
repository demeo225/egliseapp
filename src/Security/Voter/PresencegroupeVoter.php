<?php

namespace App\Security\Voter;

use App\Entity\Presencegroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencegroupeVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const PRESENCEGROUPE_VIEW = 'seancegroupe_presence';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencegroupe): bool {
        return in_array($attribute, [self::EDIT, self::PRESENCEGROUPE_VIEW]) 
            && $presencegroupe instanceof Presencegroupe;
    }

    protected function voteOnAttribute(string $attribute, $presencegroupe, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        if ($this->security->isGranted('ROLE_SECRETAIRE') || $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $groupe = $presencegroupe->getGroupe();
        if (null === $groupe) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::PRESENCEGROUPE_VIEW:
                return $this->canAccess($presencegroupe, $user);
        }

        return false;
    }

    private function canAccess(Presencegroupe $presencegroupe, User $user): bool {
        $groupe = $presencegroupe->getGroupe();
        
        // Responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUser()) {
                return $user === $departement->getUser();
            }
        }

        // Responsable du groupe
        if (method_exists($groupe, 'getResponsable') && $groupe->getResponsable()) {
            return $user === $groupe->getResponsable();
        }

        // Membre du groupe
        return $groupe->getUsers()->contains($user);
    }
}