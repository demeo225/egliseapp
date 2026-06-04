<?php

namespace App\Security\Voter;

use App\Entity\Detailcotisationgroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationgroupeVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONFAMILLE_VIEW = 'cotisergroupe_detailgroupe';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detailgroupe): bool {
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONFAMILLE_VIEW]) 
            && $detailgroupe instanceof Detailcotisationgroupe;
    }

    protected function voteOnAttribute(string $attribute, $detailgroupe, TokenInterface $token): bool {
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

        // Vérifier si le groupe existe
        $groupe = $detailgroupe->getGroupe();
        if (null === $groupe) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($detailgroupe, $user);
            case self::DETAILCOTISATIONFAMILLE_VIEW:
                return $this->canViewDetail($detailgroupe, $user);
        }

        return false;
    }

    private function canEdit(Detailcotisationgroupe $detailgroupe, User $user): bool {
        $groupe = $detailgroupe->getGroupe();
        
        // Responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUser()) {
                return $user === $departement->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable du groupe (si cette notion existe)
        if (method_exists($groupe, 'getUsers') && $groupe->getUsers()) {
            return $user === $groupe->getUsers();
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canViewDetail(Detailcotisationgroupe $detailgroupe, User $user): bool {
        $groupe = $detailgroupe->getGroupe();
        
        // Responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUser()) {
                return $user === $departement->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable du groupe
        if (method_exists($groupe, 'getUsers') && $groupe->getUsers()) {
            return $user === $groupe->getUsers();
        }

        // Vérifier si l'utilisateur appartient au groupe
        return $groupe->getUsers()->contains($user);
    }
}