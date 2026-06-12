<?php

namespace App\Security\Voter;

use App\Entity\Depensecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DepensecelluleVoter extends Voter {

    public const DEPENSE_VIEW = 'depensecellule_view';
    public const DEPENSE_EDIT = 'depensecellule_edit';
    public const DEPENSE_DELETE = 'depensecellule_delete';
    public const DEPENSE_CREATE = 'depensecellule_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $depensecellule): bool {
        return in_array($attribute, [
            self::DEPENSE_VIEW, 
            self::DEPENSE_EDIT, 
            self::DEPENSE_DELETE,
            self::DEPENSE_CREATE
        ]) && ($depensecellule instanceof Depensecellule || $depensecellule === null);
    }

    protected function voteOnAttribute(string $attribute, $depensecellule, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        // Ces rôles voient TOUTES les dépenses de l'église
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $depensecellule, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les dépenses des cellules de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $depensecellule, $user);
        }
        
        // ROLE_RESPONSABLE_CELLULE : voit uniquement les dépenses de sa cellule
        if ($this->security->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            return $this->canViewByCellule($attribute, $depensecellule, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les dépenses des cellules de sa zone
     */
    private function canViewByZone(string $attribute, ?Depensecellule $depensecellule, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de dépense spécifique)
        if ($depensecellule === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $cellule = $depensecellule->getCellule();
        if (!$cellule) {
            return false;
        }
        
        $celluleZone = $cellule->getZone();
        if (!$celluleZone || $celluleZone->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensecellule, $user);
    }
    
    /**
     * Responsable de cellule : voit uniquement sa cellule
     */
    private function canViewByCellule(string $attribute, ?Depensecellule $depensecellule, User $user): bool {
        $celluleUser = $user->getCellule();
        if (!$celluleUser) {
            return false;
        }
        
        // Pour la création (pas de dépense spécifique)
        if ($depensecellule === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $cellule = $depensecellule->getCellule();
        if (!$cellule || $cellule->getId() !== $celluleUser->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensecellule, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Depensecellule $depensecellule, User $user): bool {
        switch ($attribute) {
            case self::DEPENSE_VIEW:
                return true;
                
            case self::DEPENSE_CREATE:
                return $this->canCreate($depensecellule, $user);
                
            case self::DEPENSE_EDIT:
                return $this->canEdit($depensecellule, $user);
                
            case self::DEPENSE_DELETE:
                return $this->canDelete($depensecellule, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une dépense
     */
    private function canCreate(?Depensecellule $depensecellule, User $user): bool {
        // Les rôles supérieurs peuvent créer
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return true;
        }
        
        // Responsable de cellule
        if ($this->security->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une dépense
     */
    private function canEdit(Depensecellule $depensecellule, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $cellule = $depensecellule->getCellule();
        if (!$cellule) {
            return false;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $celluleZone = $cellule->getZone();
            if ($zone && $celluleZone && $zone->getId() === $celluleZone->getId()) {
                return true;
            }
        }
        
        // Responsable de cellule
        if ($this->security->isGranted('ROLE_RESPONSABLE_CELLULE')) {
            $celluleUser = $user->getCellule();
            if ($celluleUser && $celluleUser->getId() === $cellule->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une dépense
     */
    private function canDelete(Depensecellule $depensecellule, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($depensecellule, $user);
    }
}