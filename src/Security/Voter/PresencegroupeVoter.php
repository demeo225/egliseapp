<?php

namespace App\Security\Voter;

use App\Entity\Presencegroupe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencegroupeVoter extends Voter {

    public const PRESENCE_VIEW = 'seancegroupe_presence';
    public const PRESENCE_DELETE = 'seancegroupe_presence_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencegroupe): bool {
        return in_array($attribute, [self::PRESENCE_VIEW, self::PRESENCE_DELETE]) 
            && $presencegroupe instanceof Presencegroupe;
    }

    protected function voteOnAttribute(string $attribute, $presencegroupe, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE voient TOUTES les présences
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $presencegroupe, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT voit les présences des groupes de son département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $presencegroupe, $user);
        }
        
        // ROLE_RESPONSABLE_GROUPE voit uniquement les présences de son groupe
        if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            return $this->canViewByGroupe($attribute, $presencegroupe, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de département : voit les présences des groupes de son département
     */
    private function canViewByDepartement(string $attribute, Presencegroupe $presencegroupe, User $user): bool {
        $groupe = $presencegroupe->getGroupe();
        if (!$groupe) {
            return false;
        }
        
        $departement = $groupe->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Vérifier que le département appartient au responsable
        $departementResponsable = $departement->getUsers();
        if (!$departementResponsable || $departementResponsable !== $user) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $presencegroupe, $user);
    }
    
    /**
     * Responsable de groupe : voit uniquement son groupe
     */
    private function canViewByGroupe(string $attribute, Presencegroupe $presencegroupe, User $user): bool {
        $groupe = $presencegroupe->getGroupe();
        if (!$groupe) {
            return false;
        }
        
        // Vérifier que l'utilisateur est responsable de ce groupe
        $groupeResponsable = $groupe->getUsers();
        if ($groupeResponsable && $groupeResponsable === $user) {
            return $this->checkAttribute($attribute, $presencegroupe, $user);
        }
        
        // Vérifier si l'utilisateur appartient au groupe (pour les membres)
        if (method_exists($groupe, 'getUsers') && $groupe->getUsers()->contains($user)) {
            return $this->checkAttribute($attribute, $presencegroupe, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, Presencegroupe $presencegroupe, User $user): bool {
        switch ($attribute) {
            case self::PRESENCE_VIEW:
                return true;
                
            case self::PRESENCE_DELETE:
                return $this->canDelete($presencegroupe, $user);
        }
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer
     */
    private function canDelete(Presencegroupe $presencegroupe, User $user): bool {
        // Les rôles supérieurs peuvent tout supprimer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $groupe = $presencegroupe->getGroupe();
        if (!$groupe) {
            return false;
        }
        
        // Responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $groupe->getDepartement();
            if ($departement && $departement->getUsers() === $user) {
                return true;
            }
        }
        
        // Responsable de groupe
        if ($this->security->isGranted('ROLE_RESPONSABLE_GROUPE')) {
            if ($groupe->getUsers() === $user) {
                return true;
            }
        }
        
        return false;
    }
}