<?php

namespace App\Security\Voter;

use App\Entity\Cotiserdepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotiserdepartementVoter extends Voter {

    public const COTISATIONDEPARTEMENT_EDIT = 'cotiserdepartement_edit';
    public const COTISATIONDEPARTEMENT_VIEW = 'cotiserdepartement_index';
    public const COTISATIONDEPARTEMENT_DELETE = 'cotiserdepartement_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotiserdepartement): bool {
        return in_array($attribute, [self::COTISATIONDEPARTEMENT_EDIT, self::COTISATIONDEPARTEMENT_VIEW, self::COTISATIONDEPARTEMENT_DELETE]) 
            && $cotiserdepartement instanceof Cotiserdepartement;
    }

    protected function voteOnAttribute(string $attribute, $cotiserdepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la cotisation département existe et a un département
        $cotisationDep = $cotiserdepartement->getCotisationdepartement();
        if (null === $cotisationDep) {
            return false;
        }
        
        $departement = $cotisationDep->getDepartement();
        if (null === $departement) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONDEPARTEMENT_EDIT:
                return $this->canEdit($cotiserdepartement, $user);
            case self::COTISATIONDEPARTEMENT_VIEW:
                return $this->canView($cotiserdepartement, $user);
            case self::COTISATIONDEPARTEMENT_DELETE:
                return $this->canDelete($cotiserdepartement, $user);
        }

        return false;
    }

    private function canEdit(Cotiserdepartement $cotiserdepartement, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $departement = $cotiserdepartement->getCotisationdepartement()->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($departement->getUsers() && $user === $departement->getUsers()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient au département
        if (method_exists($departement, 'getUsers')) {
            return $departement->getUsers()->contains($user);
        }
        
        return false;
    }

    private function canView(Cotiserdepartement $cotiserdepartement, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $departement = $cotiserdepartement->getCotisationdepartement()->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de département
        if ($departement->getUsers() && $user === $departement->getUsers()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient au département
        if (method_exists($departement, 'getUsers')) {
            return $departement->getUsers()->contains($user);
        }
        
        return false;
    }

    private function canDelete(Cotiserdepartement $cotiserdepartement, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $departement = $cotiserdepartement->getCotisationdepartement()->getDepartement();
        
        // Seul le responsable de département peut supprimer
        return $departement->getUsers() && $user === $departement->getUsers();
    }
}