<?php

namespace App\Security\Voter;

use App\Entity\Seancegroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancegroupeVoter extends Voter {

    public const SEANCECELLULE_EDIT = 'seancegroupe_edit';
    public const SEANCECELLULE_VIEW = 'seancegroupe_index';
    public const SEANCECELLULE_DELETE = 'seancegroupe_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancegroupe): bool {
        return in_array($attribute, [self::SEANCECELLULE_EDIT, self::SEANCECELLULE_VIEW, self::SEANCECELLULE_DELETE]) 
            && $seancegroupe instanceof Seancegroupe;
    }

    protected function voteOnAttribute(string $attribute, $seancegroupe, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la groupe existe
        $groupe = $seancegroupe->getGroupe();
        if (null === $groupe) {
            return false;
        }

        switch ($attribute) {
            case self::SEANCECELLULE_EDIT:
                return $this->canEdit($seancegroupe, $user);
            case self::SEANCECELLULE_VIEW:
                return $this->canView($seancegroupe, $user);
            case self::SEANCECELLULE_DELETE:
                return $this->canDelete($seancegroupe, $user);
        }

        return false;
    }

    private function canEdit(Seancegroupe $seancegroupe, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $seancegroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers()) {
                return $user === $departement->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canView(Seancegroupe $seancegroupe, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $seancegroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers()) {
                return $user === $departement->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la groupe
        return $groupe->getUsers()->contains($user);
    }

    private function canDelete(Seancegroupe $seancegroupe, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $groupe = $seancegroupe->getGroupe();
        
        // Vérifier si l'utilisateur est responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers()) {
                return $user === $departement->getUsers();
            }
        }

        // Option 1: Seul le responsable de la groupe peut supprimer
        // Si vous avez un champ 'responsable' dans Groupe
        // if ($groupe->getResponsable()) {
        //     return $user === $groupe->getResponsable();
        // }
        
        // Option 2: Tous les membres de la groupe peuvent supprimer
        return $groupe->getUsers()->contains($user);
    }
}