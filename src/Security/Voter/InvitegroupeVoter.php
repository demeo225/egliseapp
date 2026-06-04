<?php

namespace App\Security\Voter;

use App\Entity\Invitegroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitegroupeVoter extends Voter {

    public const SEANCEGROUPE_EDIT = 'invitegroupe_edit';
    public const SEANCEGROUPE_VIEW = 'invitegroupe_index';
    public const SEANCEGROUPE_DELETE = 'invitegroupe_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitegroupe): bool {
        return in_array($attribute, [
            self::SEANCEGROUPE_EDIT, 
            self::SEANCEGROUPE_VIEW, 
            self::SEANCEGROUPE_DELETE
        ]) && $invitegroupe instanceof Invitegroupe;
    }

    protected function voteOnAttribute(string $attribute, $invitegroupe, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN et ROLE_SECRETAIRE ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la séance de groupe existe
        $seancegroupe = $invitegroupe->getSeancegroupe();
        if (null === $seancegroupe) {
            return false;
        }
        
        // Vérifier si le groupe existe
        $groupe = $seancegroupe->getGroupe();
        if (null === $groupe) {
            return false;
        }

        // Vérifications communes
        $isUserInGroupe = $this->isUserInGroupe($groupe, $user);
        $isGroupeResponsable = $this->isGroupeResponsable($groupe, $user);
        $isDepartementResponsable = $this->isDepartementResponsable($groupe, $user);
        $isInvitee = $this->isInvitee($invitegroupe, $user);

        switch ($attribute) {
            case self::SEANCEGROUPE_VIEW:
                return $isUserInGroupe || $isGroupeResponsable || $isDepartementResponsable || $isInvitee;
                
            case self::SEANCEGROUPE_EDIT:
                return $isUserInGroupe || $isGroupeResponsable || $isDepartementResponsable;
                
            case self::SEANCEGROUPE_DELETE:
                // Seul le responsable du groupe ou le responsable du département peut supprimer
                return $isGroupeResponsable || $isDepartementResponsable;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur appartient au groupe
     */
    private function isUserInGroupe($groupe, User $user): bool
    {
        // Vérifier via la collection getUsers
        if (!method_exists($groupe, 'getUsers')) {
            return false;
        }
        
        $users = $groupe->getUsers();
        if (null === $users) {
            return false;
        }
        
        return $users->contains($user);
    }

    /**
     * Vérifie si l'utilisateur est responsable du groupe
     */
    private function isGroupeResponsable($groupe, User $user): bool
    {
        // Vérifier si la méthode getUsers existe
        if (!method_exists($groupe, 'getUsers')) {
            return false;
        }
        
        $responsable = $groupe->getUsers();
        if (null === $responsable) {
            return false;
        }
        
        return $user === $responsable;
    }

    /**
     * Vérifie si l'utilisateur est responsable du département du groupe
     */
    private function isDepartementResponsable($groupe, User $user): bool
    {
        // Vérifier si l'utilisateur a le rôle responsable de département
        if (!$this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return false;
        }
        
        if (!method_exists($groupe, 'getDepartement')) {
            return false;
        }
        
        $departement = $groupe->getDepartement();
        if (null === $departement) {
            return false;
        }
        
        if (!method_exists($departement, 'getUser')) {
            return false;
        }
        
        $departementResponsable = $departement->getUsers();
        if (null === $departementResponsable) {
            return false;
        }
        
        return $user === $departementResponsable;
    }

    /**
     * Vérifie si l'utilisateur est l'invité lui-même
     */
    private function isInvitee(Invitegroupe $invitegroupe, User $user): bool
    {
        $invite = $invitegroupe->getInvitegroupe();
        if (null === $invite) {
            return false;
        }
        
        return $user === $invite;
    }
}