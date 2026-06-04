<?php

namespace App\Security\Voter;

use App\Entity\Cotisationcellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisationcelluleVoter extends Voter {

    public const COTISATIONCELLULE_EDIT = 'cotisationcellule_edit';
    public const COTISATIONCELLULE_VIEW = 'cotisationcellule_index';
    public const COTISATIONCELLULE_DELETE = 'cotisationcellule_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisationcellule): bool {
        return in_array($attribute, [self::COTISATIONCELLULE_EDIT, self::COTISATIONCELLULE_VIEW, self::COTISATIONCELLULE_DELETE]) 
            && $cotisationcellule instanceof Cotisationcellule;
    }

    protected function voteOnAttribute(string $attribute, $cotisationcellule, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la cellule existe
        $cellule = $cotisationcellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONCELLULE_EDIT:
                return $this->canEdit($cotisationcellule, $user);
            case self::COTISATIONCELLULE_VIEW:
                return $this->canView($cotisationcellule, $user);
            case self::COTISATIONCELLULE_DELETE:
                return $this->canDelete($cotisationcellule, $user);
        }

        return false;
    }

    private function canEdit(Cotisationcellule $cotisationcellule, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $cotisationcellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $responsableZone = $cellule->getZone()->getUsers();
            return $user === $responsableZone;
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canView(Cotisationcellule $cotisationcellule, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $cotisationcellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $responsableZone = $cellule->getZone()->getUsers();
            return $user === $responsableZone;
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canDelete(Cotisationcellule $cotisationcellule, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $cotisationcellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $responsableZone = $cellule->getZone()->getUsers();
            return $user === $responsableZone;
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }
}