<?php

namespace App\Security\Voter;

use App\Entity\Cotisergroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisergroupeVoter extends Voter {

    public const COTISATIONGROUPE_EDIT = 'cotisergroupe_edit';
    public const COTISATIONGROUPE_VIEW = 'cotisergroupe_index';
    public const COTISATIONGROUPE_DELETE = 'cotisergroupe_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisergroupe): bool {
        return in_array($attribute, [self::COTISATIONGROUPE_EDIT, self::COTISATIONGROUPE_VIEW, self::COTISATIONGROUPE_DELETE]) 
            && $cotisergroupe instanceof Cotisergroupe;
    }

    protected function voteOnAttribute(string $attribute, $cotisergroupe, TokenInterface $token): bool {
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
        $groupe = $cotisergroupe->getGroupe();
        if (null === $groupe) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONGROUPE_EDIT:
                return $this->canEdit($cotisergroupe, $user);
            case self::COTISATIONGROUPE_VIEW:
                return $this->canView($cotisergroupe, $user);
            case self::COTISATIONGROUPE_DELETE:
                return $this->canDelete($cotisergroupe, $user);
        }

        return false;
    }

    private function canEdit(Cotisergroupe $cotisergroupe, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $cotisergroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers()) {
                return $user === $departement->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable du groupe (si cette notion existe)
        if (method_exists($groupe, 'getUsers') && $groupe->getUsers()) {
            return $user === $groupe->getUsers();
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canView(Cotisergroupe $cotisergroupe, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $cotisergroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers()) {
                return $user === $departement->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable du groupe
        if (method_exists($groupe, 'getUsers') && $groupe->getUsers()) {
            return $user === $groupe->getUsers();
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canDelete(Cotisergroupe $cotisergroupe, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $cotisergroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers()) {
                return $user === $departement->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable du groupe
        if (method_exists($groupe, 'getUsers') && $groupe->getUsers()) {
            return $user === $groupe->getUsers();
        }

        // Pour la suppression, peut-être seulement le responsable du groupe
        // À adapter selon votre logique métier
        return $groupe->getUsers()->contains($user);
    }
}