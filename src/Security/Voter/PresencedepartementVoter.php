<?php

namespace App\Security\Voter;

use App\Entity\Presencedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PresencedepartementVoter extends Voter 
{
    public const COTISATIONDEPARTEMENT_EDIT = 'presencedepartement_edit';
    public const COTISATIONDEPARTEMENT_VIEW = 'presencedepartement_index';
    public const COTISATIONDEPARTEMENT_DELETE = 'presencedepartement_delete';

    private Security $security;

    public function __construct(Security $security) 
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencedepartement): bool 
    {
        return in_array($attribute, [
            self::COTISATIONDEPARTEMENT_EDIT, 
            self::COTISATIONDEPARTEMENT_VIEW, 
            self::COTISATIONDEPARTEMENT_DELETE
        ]) && $presencedepartement instanceof Presencedepartement;
    }

    protected function voteOnAttribute(string $attribute, $presencedepartement, TokenInterface $token): bool 
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $departement = $presencedepartement->getDepartement();
        
        if (null === $departement) {
            return false;
        }

        // Vérifier si l'utilisateur a accès à cette presence
        if (!$this->canAccessPresence($user, $departement)) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONDEPARTEMENT_VIEW:
                return true;
                
            case self::COTISATIONDEPARTEMENT_EDIT:
                return $this->canEdit($user, $departement);
                
            case self::COTISATIONDEPARTEMENT_DELETE:
                return $this->canDelete($user, $departement);
                
            default:
                return false;
        }
    }

    /**
     * Vérifie si l'utilisateur peut accéder à la presence
     * Accès si : 
     * - L'utilisateur appartient au departement (User.departement)
     * - OU l'utilisateur est responsable de la Departement de cette departement (User.Departement)
     */
    private function canAccessPresence(User $user, $departement): bool
    {
        // Cas 1: L'utilisateur est membre de la departement
        if ($user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
            return true;
        }
        
      
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut modifier
     * Modification possible si :
     * - L'utilisateur est secrétaire
     * - L'utilisateur est responsable de la Departement
     * - L'utilisateur est membre de la departement (si vous autorisez)
     */
    private function canEdit(User $user, $departement): bool
    {
        // Secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
      
        
        // Option: Les membres de la departement peuvent modifier
        // Décommentez si vous voulez autoriser les membres à modifier
        if ($user->getDepartement() && $user->getDepartement()->getId() === $departement->getId()) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur peut supprimer
     * Suppression possible seulement pour :
     * - Secrétaire
     * - Responsable de Departement
     */
    private function canDelete(User $user, $departement): bool
    {
        // Secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
       
        
        return false;
    }
}