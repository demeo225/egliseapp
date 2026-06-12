<?php

namespace App\Security\Voter;

use App\Entity\Cotiserzone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotiserzoneVoter extends Voter {

    public const PAIEMENT_VIEW = 'cotiserzone_view';
    public const PAIEMENT_EDIT = 'cotiserzone_edit';
    public const PAIEMENT_DELETE = 'cotiserzone_delete';
    public const PAIEMENT_CREATE = 'cotiserzone_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotiserzone): bool {
        return in_array($attribute, [
            self::PAIEMENT_VIEW, 
            self::PAIEMENT_EDIT, 
            self::PAIEMENT_DELETE,
            self::PAIEMENT_CREATE
        ]) && ($cotiserzone instanceof Cotiserzone || $cotiserzone === null);
    }

    protected function voteOnAttribute(string $attribute, $cotiserzone, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $cotiserzone, $user);
        }
        
        // ROLE_RESPONSABLE_ZONE : voit les paiements des cotisations de sa zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $this->canViewByZone($attribute, $cotiserzone, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de zone : voit les paiements des cotisations de sa zone
     */
    private function canViewByZone(string $attribute, ?Cotiserzone $cotiserzone, User $user): bool {
        $zone = $user->getZone();
        if (!$zone) {
            return false;
        }
        
        // Pour la création (pas de paiement spécifique)
        if ($cotiserzone === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        // Vérifier via la cotisation associée
        $cotisationzone = $cotiserzone->getCotisationzone();
        if ($cotisationzone) {
            $cotisationZone = $cotisationzone->getZone();
            if ($cotisationZone && $cotisationZone->getId() === $zone->getId()) {
                return $this->checkAttribute($attribute, $cotiserzone, $user);
            }
        }
        
        // Vérifier via la zone directe
        $paiementZone = $cotiserzone->getZone();
        if ($paiementZone && $paiementZone->getId() === $zone->getId()) {
            return $this->checkAttribute($attribute, $cotiserzone, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Cotiserzone $cotiserzone, User $user): bool {
        switch ($attribute) {
            case self::PAIEMENT_VIEW:
                return true;
                
            case self::PAIEMENT_CREATE:
                return $this->canCreate($cotiserzone, $user);
                
            case self::PAIEMENT_EDIT:
                return $this->canEdit($cotiserzone, $user);
                
            case self::PAIEMENT_DELETE:
                return $this->canDelete($cotiserzone, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer un paiement
     */
    private function canCreate(?Cotiserzone $cotiserzone, User $user): bool {
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
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut modifier un paiement
     */
    private function canEdit(Cotiserzone $cotiserzone, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $user->getZone();
            $cotisationzone = $cotiserzone->getCotisationzone();
            if ($cotisationzone) {
                $cotisationZone = $cotisationzone->getZone();
                if ($zone && $cotisationZone && $zone->getId() === $cotisationZone->getId()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer un paiement
     */
    private function canDelete(Cotiserzone $cotiserzone, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($cotiserzone, $user);
    }
}