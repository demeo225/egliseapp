<?php

namespace App\Security\Voter;

use App\Entity\Seancecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancecelluleVoter extends Voter {

    public const SEANCECELLULE_EDIT = 'seancecellule_edit';
    public const SEANCECELLULE_VIEW = 'seancecellule_index';
    public const SEANCECELLULE_DELETE = 'seancecellule_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancecellule): bool {
        return in_array($attribute, [self::SEANCECELLULE_EDIT, self::SEANCECELLULE_VIEW, self::SEANCECELLULE_DELETE]) 
            && $seancecellule instanceof Seancecellule;
    }

    protected function voteOnAttribute(string $attribute, $seancecellule, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la cellule existe
        $cellule = $seancecellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        switch ($attribute) {
            case self::SEANCECELLULE_EDIT:
                return $this->canEdit($seancecellule, $user);
            case self::SEANCECELLULE_VIEW:
                return $this->canView($seancecellule, $user);
            case self::SEANCECELLULE_DELETE:
                return $this->canDelete($seancecellule, $user);
        }

        return false;
    }

    private function canEdit(Seancecellule $seancecellule, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $seancecellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canView(Seancecellule $seancecellule, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $seancecellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canDelete(Seancecellule $seancecellule, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $seancecellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Option 1: Seul le responsable de la cellule peut supprimer
        // Si vous avez un champ 'responsable' dans Cellule
        // if ($cellule->getResponsable()) {
        //     return $user === $cellule->getResponsable();
        // }
        
        // Option 2: Tous les membres de la cellule peuvent supprimer
        return $cellule->getUsers()->contains($user);
    }
}