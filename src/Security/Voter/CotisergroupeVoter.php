<?php

namespace App\Security\Voter;

use App\Entity\Cotisergroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CotisergroupeVoter extends Voter 
{
    public const COTISATIONGROUPE_EDIT = 'cotisergroupe_edit';
    public const COTISATIONGROUPE_VIEW = 'cotisergroupe_index';
    public const COTISATIONGROUPE_DELETE = 'cotisergroupe_delete';

    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisergroupe): bool 
    {
        return in_array($attribute, [
            self::COTISATIONGROUPE_EDIT, 
            self::COTISATIONGROUPE_VIEW, 
            self::COTISATIONGROUPE_DELETE
        ]) && $cotisergroupe instanceof Cotisergroupe;
    }

    protected function voteOnAttribute(string $attribute, $cotisergroupe, TokenInterface $token): bool 
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $groupe = $cotisergroupe->getGroupe();
        
        if (null === $groupe) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à cette cotiser
        if (!$this->canAccessCotiser($user, $groupe)) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONGROUPE_VIEW:
                return true;
                
            case self::COTISATIONGROUPE_EDIT:
                return $this->canEdit($user, $groupe);
                
            case self::COTISATIONGROUPE_DELETE:
                return $this->canDelete($user, $groupe);
                
            default:
                return false;
        }
    }

    /**
     * Vérifie si l'utilisateur peut accéder à la cotiser
     * Accès si : 
     * - L'utilisateur appartient à la groupe (User.groupe)
     * - OU l'utilisateur est responsable de la departement de cette groupe (User.departement)
     */
    private function canAccessCotiser(User $user, $groupe): bool
    {
        // Cas 1: L'utilisateur est membre de la groupe
        if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
            return true;
        }
        
        // Cas 2: L'utilisateur est responsable de la departement qui contient cette groupe
        $departement = $groupe->getDepartement();
        if ($departement && $user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut modifier
     * Modification possible si :
     * - L'utilisateur est secrétaire
     * - L'utilisateur est responsable de la departement
     * - L'utilisateur est membre de la groupe (si vous autorisez)
     */
    private function canEdit(User $user, $groupe): bool
    {
        // Secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement peut modifier les cotisers des groupes de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $departement = $groupe->getDepartement();
            if ($departement && $user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
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
     * - Responsable de departement
     */
    private function canDelete(User $user, $groupe): bool
    {
        // Secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement peut supprimer les cotisers des groupes de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $departement = $groupe->getDepartement();
            if ($departement && $user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
                return true;
            }
        }
        
        return false;
    }
}