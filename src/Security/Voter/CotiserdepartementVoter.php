<?php

namespace App\Security\Voter;

use App\Entity\Cotiserdepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotiserdepartementVoter extends Voter {

    public const PAIEMENT_VIEW = 'cotiserdepartement_view';
    public const PAIEMENT_EDIT = 'cotiserdepartement_edit';
    public const PAIEMENT_DELETE = 'cotiserdepartement_delete';
    public const PAIEMENT_CREATE = 'cotiserdepartement_create';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotiserdepartement): bool {
        return in_array($attribute, [
            self::PAIEMENT_VIEW, 
            self::PAIEMENT_EDIT, 
            self::PAIEMENT_DELETE,
            self::PAIEMENT_CREATE
        ]) && ($cotiserdepartement instanceof Cotiserdepartement || $cotiserdepartement === null);
    }

    protected function voteOnAttribute(string $attribute, $cotiserdepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLES SUPERIEURS : ROLE_ADMIN, ROLE_PASTEUR, ROLE_SECRETAIRE
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return $this->checkAttribute($attribute, $cotiserdepartement, $user);
        }
        
        // ROLE_RESPONSABLE_DEPARTEMENT : voit les paiements des cotisations de sa departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            return $this->canViewByDepartement($attribute, $cotiserdepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Responsable de departement : voit les paiements des cotisations de sa departement
     */
    private function canViewByDepartement(string $attribute, ?Cotiserdepartement $cotiserdepartement, User $user): bool {
        $departement = $user->getDepartement();
        if (!$departement) {
            return false;
        }
        
        // Pour la création (pas de paiement spécifique)
        if ($cotiserdepartement === null) {
            return $this->checkAttribute($attribute, null, $user);
        }
        
        // Vérifier via la cotisation associée
        $cotisationdepartement = $cotiserdepartement->getCotisationdepartement();
        if ($cotisationdepartement) {
            $cotisationDepartement = $cotisationdepartement->getDepartement();
            if ($cotisationDepartement && $cotisationDepartement->getId() === $departement->getId()) {
                return $this->checkAttribute($attribute, $cotiserdepartement, $user);
            }
        }
        
        // Vérifier via la departement directe
        $paiementDepartement = $cotiserdepartement->getDepartement();
        if ($paiementDepartement && $paiementDepartement->getId() === $departement->getId()) {
            return $this->checkAttribute($attribute, $cotiserdepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie le type d'action
     */
    private function checkAttribute(string $attribute, ?Cotiserdepartement $cotiserdepartement, User $user): bool {
        switch ($attribute) {
            case self::PAIEMENT_VIEW:
                return true;
                
            case self::PAIEMENT_CREATE:
                return $this->canCreate($cotiserdepartement, $user);
                
            case self::PAIEMENT_EDIT:
                return $this->canEdit($cotiserdepartement, $user);
                
            case self::PAIEMENT_DELETE:
                return $this->canDelete($cotiserdepartement, $user);
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut créer un paiement
     */
    private function canCreate(?Cotiserdepartement $cotiserdepartement, User $user): bool {
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
     * Vérifie si l'utilisateur peut modifier un paiement
     */
    private function canEdit(Cotiserdepartement $cotiserdepartement, User $user): bool {
        // Les rôles supérieurs peuvent tout modifier
        if ($this->security->isGranted('ROLE_ADMIN') || 
            $this->security->isGranted('ROLE_PASTEUR') || 
            $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // Responsable de departement
        if ($this->security->isGranted('ROLE_RESPONSABLE_DEPARTEMENT')) {
            $departement = $user->getDepartement();
            $cotisationdepartement = $cotiserdepartement->getCotisationdepartement();
            if ($cotisationdepartement) {
                $cotisationDepartement = $cotisationdepartement->getDepartement();
                if ($departement && $cotisationDepartement && $departement->getId() === $cotisationDepartement->getId()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Vérifie si l'utilisateur peut supprimer un paiement
     */
    private function canDelete(Cotiserdepartement $cotiserdepartement, User $user): bool {
        // Même logique que l'édition
        return $this->canEdit($cotiserdepartement, $user);
    }
}