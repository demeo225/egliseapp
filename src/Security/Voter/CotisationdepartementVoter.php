<?php

namespace App\Security\Voter;

use App\Entity\Cotisationdepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationdepartementVoter extends Voter {

    public const COTISATIONDEPARTEMENT_EDIT = 'cotisationdepartement_edit';
    public const COTISATIONDEPARTEMENT_VIEW = 'cotisationdepartement_index';
    public const COTISATIONDEPARTEMENT_DELETE = 'cotisationdepartement_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationdepartement): bool {
        return in_array($attribute, [self::COTISATIONDEPARTEMENT_EDIT, self::COTISATIONDEPARTEMENT_VIEW, self::COTISATIONDEPARTEMENT_DELETE]) 
            && $cotisationdepartement instanceof Cotisationdepartement;
    }

    protected function voteOnAttribute(string $attribute, $cotisationdepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si le département existe
        $departement = $cotisationdepartement->getDepartement();
        if (null === $departement) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONDEPARTEMENT_EDIT:
                return $this->canEdit($cotisationdepartement, $user);
            case self::COTISATIONDEPARTEMENT_VIEW:
                return $this->canView($cotisationdepartement, $user);
            case self::COTISATIONDEPARTEMENT_DELETE:
                return $this->canDelete($cotisationdepartement, $user);
        }

        return false;
    }

    private function canEdit(Cotisationdepartement $cotisationdepartement, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $departement = $cotisationdepartement->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone (si cette relation existe)
        // À adapter selon votre structure
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            // Si le département a une relation avec une zone
            if ($departement->getZone() && $departement->getZone()->getUsers()) {
                return $user === $departement->getZone()->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient au département
        // Note: Cette méthode suppose que Departement a une relation getUsers() similaire à Cellule
        // Si ce n'est pas le cas, il faudra adapter
        return $departement->getUsers()->contains($user);
    }

    private function canView(Cotisationdepartement $cotisationdepartement, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $departement = $cotisationdepartement->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            if ($departement->getZone() && $departement->getZone()->getUsers()) {
                return $user === $departement->getZone()->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient au département
        return $departement->getUsers()->contains($user);
    }

    private function canDelete(Cotisationdepartement $cotisationdepartement, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $departement = $cotisationdepartement->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            if ($departement->getZone() && $departement->getZone()->getUsers()) {
                return $user === $departement->getZone()->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient au département
        return $departement->getUsers()->contains($user);
    }
}