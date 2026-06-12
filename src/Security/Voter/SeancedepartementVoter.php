<?php

namespace App\Security\Voter;

use App\Entity\Seancedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancedepartementVoter extends Voter {

    public const SEANCE_VIEW = 'seancedepartement_view';
    public const SEANCE_EDIT = 'seancedepartement_edit';
    public const SEANCE_DELETE = 'seancedepartement_delete';
    public const SEANCE_CREATE = 'seancedepartement_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancedepartement): bool {
        return in_array($attribute, [
            self::SEANCE_VIEW, 
            self::SEANCE_EDIT, 
            self::SEANCE_DELETE,
            self::SEANCE_CREATE
        ]) && ($seancedepartement instanceof Seancedepartement || $seancedepartement === null);
    }

    protected function voteOnAttribute(string $attribute, $seancedepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $seancedepartement, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les séances de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $seancedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de departement : voit les séances de sa departement
     */
    private function canViewByDepartement(string $attribute, ?Seancedepartement $seancedepartement, User $user): bool {
        $departement = $user->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Pour la création (pas de séance spécifique)
        if ($seancedepartement === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $seanceDepartement = $seancedepartement->getDepartement();
        if (!$seanceDepartement || $seanceDepartement->getId() !== $departement->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $seancedepartement, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Seancedepartement $seancedepartement, User $user): bool {
        switch ($attribute) {
            case self::SEANCE_VIEW:
                return true;
                
            case self::SEANCE_CREATE:
                return $this->canCreate($seancedepartement, $user);
                
            case self::SEANCE_EDIT:
                return $this->canEdit($seancedepartement, $user);
                
            case self::SEANCE_DELETE:
                return $this->canDelete($seancedepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une séance
     */
    private function canCreate(?Seancedepartement $seancedepartement, User $user): bool {
        // Les rôles supérieurs peuvent créer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une séance
     */
    private function canEdit(Seancedepartement $seancedepartement, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $user->getDepartement();
            $seanceDepartement = $seancedepartement->getDepartement();
            if ($departement && $seanceDepartement && $departement->getId() === $seanceDepartement->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une séance
     */
    private function canDelete(Seancedepartement $seancedepartement, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($seancedepartement, $user);
    }
}