<?php

namespace App\Security\Voter;

use App\Entity\Cotisationgroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationgroupeVoter extends Voter {

    public const COTISATIONGROUPE_EDIT = 'cotisationgroupe_edit';
    public const COTISATIONGROUPE_VIEW = 'cotisationgroupe_index';
    public const COTISATIONGROUPE_DELETE = 'cotisationgroupe_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationgroupe): bool {
        return in_array($attribute, [self::COTISATIONGROUPE_EDIT, self::COTISATIONGROUPE_VIEW, self::COTISATIONGROUPE_DELETE]) 
            && $cotisationgroupe instanceof Cotisationgroupe;
    }

    protected function voteOnAttribute(string $attribute, $cotisationgroupe, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si le groupe existe
        $groupe = $cotisationgroupe->getGroupe();
        if (null === $groupe) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONGROUPE_EDIT:
                return $this->canEdit($cotisationgroupe, $user);
            case self::COTISATIONGROUPE_VIEW:
                return $this->canView($cotisationgroupe, $user);
            case self::COTISATIONGROUPE_DELETE:
                return $this->canDelete($cotisationgroupe, $user);
        }

        return false;
    }

    private function canEdit(Cotisationgroupe $cotisationgroupe, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $cotisationgroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUser()) {
                return $user === $departement->getUser();
            }
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canView(Cotisationgroupe $cotisationgroupe, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $cotisationgroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUser()) {
                return $user === $departement->getUser();
            }
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canDelete(Cotisationgroupe $cotisationgroupe, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $cotisationgroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUser()) {
                return $user === $departement->getUser();
            }
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }
}