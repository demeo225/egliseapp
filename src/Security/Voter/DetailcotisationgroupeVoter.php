<?php

namespace App\Security\Voter;

use App\Entity\Detailcotisationgroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class DetailcotisationgroupeVoter extends Voter 
{
    public const COTISATIONCELLULE_EDIT = 'detailcotisationgroupe_edit';
    public const COTISATIONCELLULE_VIEW = 'detailcotisationgroupe_index';
    public const COTISATIONCELLULE_DELETE = 'detailcotisationgroupe_delete';

    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detailcotisationgroupe): bool 
    {
        return in_array($attribute, [
            self::COTISATIONCELLULE_EDIT, 
            self::COTISATIONCELLULE_VIEW, 
            self::COTISATIONCELLULE_DELETE
        ]) && $detailcotisationgroupe instanceof Detailcotisationgroupe;
    }

    protected function voteOnAttribute(string $attribute, $detailcotisationgroupe, TokenInterface $token): bool 
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $groupe = $detailcotisationgroupe->getGroupe();
        
        if (null === $groupe) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à cette detailcotisation
        if (!$this->canAccessDetailcotisation($user, $groupe)) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONCELLULE_VIEW:
                return true;
                
            case self::COTISATIONCELLULE_EDIT:
                return $this->canEdit($user, $groupe);
                
            case self::COTISATIONCELLULE_DELETE:
                return $this->canDelete($user, $groupe);
                
            default:
                return false;
        }
    }

    /**
     * Vérifie si l'utilisateur peut accéder à la detailcotisation
     * Accès si : 
     * - L'utilisateur appartient à la groupe (User.groupe)
     * - OU l'utilisateur est responsable de la zone de cette groupe (User.zone)
     */
    private function canAccessDetailcotisation(User $user, $groupe): bool
    {
        // Cas 1: L'utilisateur est membre de la groupe
        if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
            return true;
        }
        
        // Cas 2: L'utilisateur est responsable de la zone qui contient cette groupe
        $zone = $groupe->getDepartement();
        if ($zone && $user->getDepartement() && $user->getDepartement()->getId() === $zone->getId()) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut modifier
     * Modification possible si :
     * - L'utilisateur est secrétaire
     * - L'utilisateur est responsable de la zone
     * - L'utilisateur est membre de la groupe (si vous autorisez)
     */
    private function canEdit(User $user, $groupe): bool
    {
        // Secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone peut modifier les detailcotisations des groupes de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $zone = $groupe->getDepartement();
            if ($zone && $user->getDepartement() && $user->getDepartement()->getId() === $zone->getId()) {
                return true;
            }
        }
        
        // Option: Les membres de la groupe peuvent modifier
        // Décommentez si vous voulez autoriser les membres à modifier
        if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
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
    private function canDelete(User $user, $groupe): bool
    {
        // Secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone peut supprimer les detailcotisations des groupes de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $zone = $groupe->getDepartement();
            if ($zone && $user->getDepartement() && $user->getDepartement()->getId() === $zone->getId()) {
                return true;
            }
        }
        
        return false;
    }
}