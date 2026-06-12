<?php

namespace App\Security\Voter;

use App\Entity\Invitecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class InvitecelluleVoter extends Voter 
{
    public const COTISATIONCELLULE_EDIT = 'invitecellule_edit';
    public const COTISATIONCELLULE_VIEW = 'invitecellule_index';
    public const COTISATIONCELLULE_DELETE = 'invitecellule_delete';

    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitecellule): bool 
    {
        return in_array($attribute, [
            self::COTISATIONCELLULE_EDIT, 
            self::COTISATIONCELLULE_VIEW, 
            self::COTISATIONCELLULE_DELETE
        ]) && $invitecellule instanceof Invitecellule;
    }

    protected function voteOnAttribute(string $attribute, $invitecellule, TokenInterface $token): bool 
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $cellule = $invitecellule->getSeancecellule()->getCellule();
        
        if (null === $cellule) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à cette invite
        if (!$this->canAccessInvite($user, $cellule)) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONCELLULE_VIEW:
                return true;
                
            case self::COTISATIONCELLULE_EDIT:
                return $this->canEdit($user, $cellule);
                
            case self::COTISATIONCELLULE_DELETE:
                return $this->canDelete($user, $cellule);
                
            default:
                return false;
        }
    }

    /**
     * Vérifie si l'utilisateur peut accéder à la invite
     * Accès si : 
     * - L'utilisateur appartient à la cellule (User.cellule)
     * - OU l'utilisateur est responsable de la zone de cette cellule (User.zone)
     */
    private function canAccessInvite(User $user, $cellule): bool
    {
        // Cas 1: L'utilisateur est membre de la cellule
        if ($user->getCellule() && $user->getCellule()->getId() === $cellule->getId()) {
            return true;
        }
        
        // Cas 2: L'utilisateur est responsable de la zone qui contient cette cellule
        $zone = $cellule->getZone();
        if ($zone && $user->getZone() && $user->getZone()->getId() === $zone->getId()) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut modifier
     * Modification possible si :
     * - L'utilisateur est secrétaire
     * - L'utilisateur est responsable de la zone
     * - L'utilisateur est membre de la cellule (si vous autorisez)
     */
    private function canEdit(User $user, $cellule): bool
    {
        // Secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone peut modifier les invites des cellules de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $user->getZone() && $user->getZone()->getId() === $zone->getId()) {
                return true;
            }
        }
        
        // Option: Les membres de la cellule peuvent modifier
        // Décommentez si vous voulez autoriser les membres à modifier
        if ($user->getCellule() && $user->getCellule()->getId() === $cellule->getId()) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut supprimer
     * Suppression possible seulement pour :
     * - Secrétaire
     * - Responsable de zone
     */
    private function canDelete(User $user, $cellule): bool
    {
        // Secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone peut supprimer les invites des cellules de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $user->getZone() && $user->getZone()->getId() === $zone->getId()) {
                return true;
            }
        }
        
        return false;
    }
}