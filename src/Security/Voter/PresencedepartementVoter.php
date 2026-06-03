<?php

namespace App\Security\Voter;

use App\Entity\Presencedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencedepartementVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const PRESENCEDEPARTEMENT_VIEW = 'seancedepartement_presence';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencedepartement): bool {
        return in_array($attribute, [self::EDIT, self::PRESENCEDEPARTEMENT_VIEW]) 
            && $presencedepartement instanceof Presencedepartement;
    }

    protected function voteOnAttribute(string $attribute, $presencedepartement, TokenInterface $token): bool {
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

        // Vérifier si le département existe
        $departement = $presencedepartement->getDepartement();
        if (null === $departement) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($presencedepartement, $user);
            case self::PRESENCEDEPARTEMENT_VIEW:
                return $this->canViewPresence($presencedepartement, $user);
        }

        return false;
    }

    private function canEdit(Presencedepartement $presencedepartement, User $user): bool {
        $departement = $presencedepartement->getDepartement();
        
        // Vérifier si l'utilisateur est le responsable du département
        if ($departement->getUser() && $user === $departement->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient au département (si c'est une collection)
        if (method_exists($departement, 'getUsers')) {
            return $departement->getUsers()->contains($user);
        }

        return false;
    }

    private function canViewPresence(Presencedepartement $presencedepartement, User $user): bool {
        $departement = $presencedepartement->getDepartement();
        
        // Le responsable du département peut voir
        if ($departement->getUser() && $user === $departement->getUser()) {
            return true;
        }

        // Les membres du département peuvent voir
        if (method_exists($departement, 'getUsers')) {
            return $departement->getUsers()->contains($user);
        }

        return false;
    }
}