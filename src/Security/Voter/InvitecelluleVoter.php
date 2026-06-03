<?php

namespace App\Security\Voter;

use App\Entity\Invitecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitecelluleVoter extends Voter {

    public const SEANCECELLULE_EDIT = 'invitecellule_edit';
    public const SEANCECELLULE_VIEW = 'invitecellule_index';
    public const SEANCECELLULE_DELETE = 'invitecellule_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitecellule): bool {
        return in_array($attribute, [
            self::SEANCECELLULE_EDIT, 
            self::SEANCECELLULE_VIEW, 
            self::SEANCECELLULE_DELETE
        ]) && $invitecellule instanceof Invitecellule;
    }

    protected function voteOnAttribute(string $attribute, $invitecellule, TokenInterface $token): bool {
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

        // Vérifier si la séance de cellule existe
        $seancecellule = $invitecellule->getSeancecellule();
        if (null === $seancecellule) {
            return false;
        }
        
        // Vérifier si la cellule existe
        $cellule = $seancecellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        // Vérification commune : l'utilisateur appartient-il à la cellule ?
        $isUserInCellule = $this->isUserInCellule($cellule, $user);
        
        // Vérification si l'utilisateur est responsable de la zone
        $isUserZoneResponsable = $this->isUserZoneResponsable($cellule, $user);

        switch ($attribute) {
            case self::SEANCECELLULE_VIEW:
                // Pour la vue, l'utilisateur doit être dans la cellule, responsable de zone, 
                // ou être l'invité lui-même
                return $isUserInCellule || $isUserZoneResponsable || $this->isInvitee($invitecellule, $user);
                
            case self::SEANCECELLULE_EDIT:
                // Pour l'édition, l'utilisateur doit être dans la cellule ou responsable de zone
                return $isUserInCellule || $isUserZoneResponsable;
                
            case self::SEANCECELLULE_DELETE:
                // Pour la suppression, l'utilisateur doit être dans la cellule ou responsable de zone
                return $isUserInCellule || $isUserZoneResponsable;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur appartient à la cellule
     */
    private function isUserInCellule($cellule, User $user): bool
    {
        // Vérifier si la cellule a des utilisateurs
        if (null === $cellule->getUsers()) {
            return false;
        }
        
        // Vérifier si l'utilisateur est dans la collection des utilisateurs de la cellule
        return $cellule->getUsers()->contains($user);
    }

    /**
     * Vérifie si l'utilisateur est responsable de la zone de la cellule
     */
    private function isUserZoneResponsable($cellule, User $user): bool
    {
        // Vérifier si l'utilisateur a le rôle responsable de zone
        if (!$this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return false;
        }
        
        $zone = $cellule->getZone();
        if (null === $zone) {
            return false;
        }
        
        // Vérifier si l'utilisateur est le responsable de la zone
        $zoneUser = $zone->getUser();
        if (null === $zoneUser) {
            return false;
        }
        
        return $user === $zoneUser;
    }

    /**
     * Vérifie si l'utilisateur est l'invité lui-même
     */
    private function isInvitee(Invitecellule $invitecellule, User $user): bool
    {
        $invite = $invitecellule->getInvitecellule();
        if (null === $invite) {
            return false;
        }
        
        return $user === $invite;
    }
}