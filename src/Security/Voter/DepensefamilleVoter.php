<?php

namespace App\Security\Voter;

use App\Entity\Depensefamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DepensefamilleVoter extends Voter {

    public const DEPENSE_VIEW = 'depensefamille_view';
    public const DEPENSE_EDIT = 'depensefamille_edit';
    public const DEPENSE_DELETE = 'depensefamille_delete';
    public const DEPENSE_CREATE = 'depensefamille_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $depensefamille): bool {
        return in_array($attribute, [
            self::DEPENSE_VIEW, 
            self::DEPENSE_EDIT, 
            self::DEPENSE_DELETE,
            self::DEPENSE_CREATE
        ]) && ($depensefamille instanceof Depensefamille || $depensefamille === null);
    }

    protected function voteOnAttribute(string $attribute, $depensefamille, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        // Ces rôles voient TOUTES les dépenses de l'église
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $depensefamille, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les dépenses des familles de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $depensefamille, $user);
        }
        
        // ROLE_RESPONSABLE_FAMILLE : voit uniquement les dépenses de sa famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            return $this->canViewByFamille($attribute, $depensefamille, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les dépenses des familles de sa zone
     */
    private function canViewByZone(string $attribute, ?Depensefamille $depensefamille, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de dépense spécifique)
        if ($depensefamille === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $famille = $depensefamille->getFamille();
        if (!$famille) {
            return false;
        }
        
        $familleZone = $famille->getZone();
        if (!$familleZone || $familleZone->getId() !== $zone->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensefamille, $user);
    }
    
    /**
     * Responsable de famille : voit uniquement sa famille
     */
    private function canViewByFamille(string $attribute, ?Depensefamille $depensefamille, User $user): bool {
        $familleUser = $user->getFamille();
        if (!$familleUser) {
            return false;
        }
        
        // Pour la création (pas de dépense spécifique)
        if ($depensefamille === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        $famille = $depensefamille->getFamille();
        if (!$famille || $famille->getId() !== $familleUser->getId()) {
            return false;
        }
        
        return $this->checkAttribute($attribute, $depensefamille, $user);
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Depensefamille $depensefamille, User $user): bool {
        switch ($attribute) {
            case self::DEPENSE_VIEW:
                return true;
                
            case self::DEPENSE_CREATE:
                return $this->canCreate($depensefamille, $user);
                
            case self::DEPENSE_EDIT:
                return $this->canEdit($depensefamille, $user);
                
            case self::DEPENSE_DELETE:
                return $this->canDelete($depensefamille, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer une dépense
     */
    private function canCreate(?Depensefamille $depensefamille, User $user): bool {
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
        
        // Responsable de famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier une dépense
     */
    private function canEdit(Depensefamille $depensefamille, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        $famille = $depensefamille->getFamille();
        if (!$famille) {
            return false;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $familleZone = $famille->getZone();
            if ($zone && $familleZone && $zone->getId() === $familleZone->getId()) {
                return true;
            }
        }
        
        // Responsable de famille
        if ($this->security->isGranted('ROLE_RESPONSABLE_FAMILLE')) {
            $familleUser = $user->getFamille();
            if ($familleUser && $familleUser->getId() === $famille->getId()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer une dépense
     */
    private function canDelete(Depensefamille $depensefamille, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($depensefamille, $user);
    }
}