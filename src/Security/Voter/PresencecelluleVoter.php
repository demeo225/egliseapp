<?php

namespace App\Security\Voter;

use App\Entity\Presencecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencecelluleVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const PRESENCEFAMILLE_VIEW = 'seancecellule_presence';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencecellule): bool {
        return in_array($attribute, [self::EDIT, self::PRESENCEFAMILLE_VIEW]) 
            && $presencecellule instanceof Presencecellule;
    }

    protected function voteOnAttribute(string $attribute, $presencecellule, TokenInterface $token): bool {
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

        // Vérifier si la cellule existe
        $cellule = $presencecellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($presencecellule, $user);
            case self::PRESENCEFAMILLE_VIEW:
                return $this->canViewPresence($presencecellule, $user);
        }

        return false;
    }

    private function canEdit(Presencecellule $presencecellule, User $user): bool {
        $cellule = $presencecellule->getCellule();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la cellule (si cette notion existe)
        if (method_exists($cellule, 'getResponsable') && $cellule->getResponsable()) {
            return $user === $cellule->getResponsable();
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canViewPresence(Presencecellule $presencecellule, User $user): bool {
        $cellule = $presencecellule->getCellule();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la cellule
        if (method_exists($cellule, 'getResponsable') && $cellule->getResponsable()) {
            return $user === $cellule->getResponsable();
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }
}