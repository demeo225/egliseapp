<?php

namespace App\Security\Voter;

use App\Entity\Invitedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitedepartementVoter extends Voter {

    public const SEANCEDEPARTEMENT_EDIT = 'invitedepartement_edit';
    public const SEANCEDEPARTEMENT_VIEW = 'invitedepartement_index';
    public const SEANCEDEPARTEMENT_DELETE = 'invitedepartement_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitedepartement): bool {
        return in_array($attribute, [
            self::SEANCEDEPARTEMENT_EDIT, 
            self::SEANCEDEPARTEMENT_VIEW, 
            self::SEANCEDEPARTEMENT_DELETE
        ]) && $invitedepartement instanceof Invitedepartement;
    }

    protected function voteOnAttribute(string $attribute, $invitedepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN et ROLE_SECRETAIRE ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la séance de département existe
        $seancedepartement = $invitedepartement->getSeancedepartement();
        if (null === $seancedepartement) {
            return false;
        }
        
        // Vérifier si le département existe
        $departement = $seancedepartement->getDepartement();
        if (null === $departement) {
            return false;
        }

        // Vérifications communes
        $isUserInDepartement = $this->isUserInDepartement($departement, $user);
        $isDepartementResponsable = $this->isDepartementResponsable($departement, $user);
        $isInvitee = $this->isInvitee($invitedepartement, $user);

        switch ($attribute) {
            case self::SEANCEDEPARTEMENT_VIEW:
                return $isUserInDepartement || $isDepartementResponsable || $isInvitee;
                
            case self::SEANCEDEPARTEMENT_EDIT:
            case self::SEANCEDEPARTEMENT_DELETE:
                return $isUserInDepartement || $isDepartementResponsable;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur appartient au département
     */
    private function isUserInDepartement($departement, User $user): bool
    {
        // Vérifier si la méthode getUsers existe
        if (!method_exists($departement, 'getUsers')) {
            return false;
        }
        
        $users = $departement->getUsers();
        if (null === $users) {
            return false;
        }
        
        return $users->contains($user);
    }

    /**
     * Vérifie si l'utilisateur est responsable du département
     */
    private function isDepartementResponsable($departement, User $user): bool
    {
        // Vérifier si l'utilisateur a le rôle responsable de département
        if (!$this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return false;
        }
        
        // Vérifier si la méthode getUser existe
        if (!method_exists($departement, 'getUser')) {
            return false;
        }
        
        $departementUser = $departement->getUser();
        if (null === $departementUser) {
            return false;
        }
        
        return $user === $departementUser;
    }

    /**
     * Vérifie si l'utilisateur est l'invité lui-même
     */
    private function isInvitee(Invitedepartement $invitedepartement, User $user): bool
    {
        $invite = $invitedepartement->getInvitedepartement();
        if (null === $invite) {
            return false;
        }
        
        return $user === $invite;
    }
}