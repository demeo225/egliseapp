<?php

namespace App\Security\Voter;

use App\Entity\Invitefamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class InvitefamilleVoter extends Voter 
{
    public const COTISATIONCELLULE_EDIT = 'invitefamille_edit';
    public const COTISATIONCELLULE_VIEW = 'invitefamille_index';
    public const COTISATIONCELLULE_DELETE = 'invitefamille_delete';

    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitefamille): bool 
    {
        return in_array($attribute, [
            self::COTISATIONCELLULE_EDIT, 
            self::COTISATIONCELLULE_VIEW, 
            self::COTISATIONCELLULE_DELETE
        ]) && $invitefamille instanceof Invitefamille;
    }

    protected function voteOnAttribute(string $attribute, $invitefamille, TokenInterface $token): bool 
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $famille = $invitefamille->getSeancefamille()->getFamille();
        
        if (null === $famille) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à cette invite
        if (!$this->canAccessInvite($user, $famille)) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONCELLULE_VIEW:
                return true;
                
            case self::COTISATIONCELLULE_EDIT:
                return $this->canEdit($user, $famille);
                
            case self::COTISATIONCELLULE_DELETE:
                return $this->canDelete($user, $famille);
                
            default:
                return false;
        }
    }

    /**
     * Vérifie si l'utilisateur peut accéder à la invite
     * Accès si : 
     * - L'utilisateur appartient à la famille (User.famille)
     * - OU l'utilisateur est responsable de la zone de cette famille (User.zone)
     */
    private function canAccessInvite(User $user, $famille): bool
    {
        // Cas 1: L'utilisateur est membre de la famille
        if ($user->getFamille() && $user->getFamille()->getId() === $famille->getId()) {
            return true;
        }
        
        // Cas 2: L'utilisateur est responsable de la zone qui contient cette famille
        $zone = $famille->getZone();
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
     * - L'utilisateur est membre de la famille (si vous autorisez)
     */
    private function canEdit(User $user, $famille): bool
    {
        // Secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone peut modifier les invites des familles de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $user->getZone() && $user->getZone()->getId() === $zone->getId()) {
                return true;
            }
        }
        
        // Option: Les membres de la famille peuvent modifier
        // Décommentez si vous voulez autoriser les membres à modifier
        if ($user->getFamille() && $user->getFamille()->getId() === $famille->getId()) {
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
    private function canDelete(User $user, $famille): bool
    {
        // Secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone peut supprimer les invites des familles de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $user->getZone() && $user->getZone()->getId() === $zone->getId()) {
                return true;
            }
        }
        
        return false;
    }
}