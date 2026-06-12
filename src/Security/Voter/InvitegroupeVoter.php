<?php

namespace App\Security\Voter;

use App\Entity\Invitegroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class InvitegroupeVoter extends Voter 
{
    public const INVITEGROUPE_EDIT = 'invitegroupe_edit';
    public const INVITEGROUPE_VIEW = 'invitegroupe_index';
    public const INVITEGROUPE_DELETE = 'invitegroupe_delete';

    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitegroupe): bool 
    {
        return in_array($attribute, [
            self::INVITEGROUPE_EDIT, 
            self::INVITEGROUPE_VIEW, 
            self::INVITEGROUPE_DELETE
        ]) && $invitegroupe instanceof Invitegroupe;
    }

    protected function voteOnAttribute(string $attribute, $invitegroupe, TokenInterface $token): bool 
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $groupe = $invitegroupe->getGroupe();
        
        if (null === $groupe) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à cette invite
        if (!$this->canAccessInvite($user, $invitegroupe)) {
            return false;
        }

        switch ($attribute) {
            case self::INVITEGROUPE_VIEW:
                return true;
                
            case self::INVITEGROUPE_EDIT:
                return $this->canEdit($user, $groupe);
                
            case self::INVITEGROUPE_DELETE:
                return $this->canDelete($user, $groupe);
                
            default:
                return false;
        }
    }

    private function canAccessInvite(User $user, Invitegroupe $invitegroupe): bool
    {
        $groupe = $invitegroupe->getGroupe();
        
        // Cas 1: L'utilisateur est membre du groupe (TOUS les membres)
        if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
            return true;
        }
        
        // Cas 2: L'utilisateur est responsable du département
        $departement = $groupe->getDepartement();
        if ($departement && $user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
            return true;
        }
        
        // Cas 3: L'utilisateur a le rôle RESPONSABLE_GROUPE (même s'il n'est pas membre?)
        // Si vous voulez que le responsable groupe voit tous les groupes, décommentez :
        // if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
        //     return true;
        // }
        
        return false;
    }

    private function canEdit(User $user, $groupe): bool
    {
        // Secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de département peut modifier
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
                return true;
            }
        }
        
        // Responsable de groupe peut modifier son groupe
        if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            // Vérifier si le responsable est bien attaché à ce groupe
            if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
                return true;
            }
        }
        
        // Les membres du groupe peuvent modifier
        if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
            return true;
        }
        
        return false;
    }

    private function canDelete(User $user, $groupe): bool
    {
        // Seulement secrétaire et responsable département peuvent supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
                return true;
            }
        }
        
        // Option: Responsable de groupe peut supprimer (si vous le souhaitez)
        // if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
        //     if ($user->getGroupe() && $user->getGroupe()->getId() === $groupe->getId()) {
        //         return true;
        //     }
        // }
        
        return false;
    }
}