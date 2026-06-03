<?php

namespace App\Security\Voter;

use App\Entity\Presencedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencedepartementVoter extends Voter {

    public const PRESENCE_VIEW = 'seancedepartement_presence';
    public const PRESENCE_DELETE = 'seancedepartement_presence_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencedepartement): bool {
        return in_array($attribute, [self::PRESENCE_VIEW, self::PRESENCE_DELETE]) 
            && $presencedepartement instanceof Presencedepartement;
    }

    protected function voteOnAttribute(string $attribute, $presencedepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE voient TOUTES les présences
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $presencedepartement, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT voit uniquement les présences de son département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $presencedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de département : voit uniquement son département
     */
    private function canViewByDepartement(string $attribute, Presencedepartement $presencedepartement, User $user): bool {
        $departement = $presencedepartement->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Vérifier que l'utilisateur est responsable de ce département
        $departementResponsable = $departement->getUsers();
        if (!$departementResponsable || $departementResponsable !== $user) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $presencedepartement, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, Presencedepartement $presencedepartement, User $user): bool {
        switch ($attribute) {
            case self::PRESENCE_VIEW:
                return true;
                
            case self::PRESENCE_DELETE:
                return $this->canDelete($presencedepartement, $user);
        }
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer
     */
    private function canDelete(Presencedepartement $presencedepartement, User $user): bool {
        // Les rôles supérieurs peuvent tout supprimer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de département
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $presencedepartement->getDepartement();
            if ($departement && $departement->getUsers() === $user) {
                return true;
            }
        }
        
        return false;
    }
}