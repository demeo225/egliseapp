<?php

namespace App\Security\Voter;

use App\Entity\Cotisercellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CotisercelluleVoter extends Voter {

    public const COTISATIONCELLULE_EDIT = 'cotisercellule_edit';
    public const COTISATIONCELLULE_VIEW = 'cotisercellule_index';
    public const COTISATIONCELLULE_DELETE = 'cotisercellule_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $cotisercellule): bool {
        return in_array($attribute, [self::COTISATIONCELLULE_EDIT, self::COTISATIONCELLULE_VIEW, self::COTISATIONCELLULE_DELETE]) 
            && $cotisercellule instanceof Cotisercellule;
    }

    protected function voteOnAttribute(string $attribute, $cotisercellule, TokenInterface $token): bool {
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
        $cellule = $cotisercellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        switch ($attribute) {
            case self::COTISATIONCELLULE_EDIT:
                return $this->canEdit($cotisercellule, $user);
            case self::COTISATIONCELLULE_VIEW:
                return $this->canView($cotisercellule, $user);
            case self::COTISATIONCELLULE_DELETE:
                return $this->canDelete($cotisercellule, $user);
        }

        return false;
    }

    private function canEdit(Cotisercellule $cotisercellule, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $cotisercellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $responsableZone = $cellule->getZone()->getUser();
            return $user === $responsableZone;
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canView(Cotisercellule $cotisercellule, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $cotisercellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $responsableZone = $cellule->getZone()->getUser();
            return $user === $responsableZone;
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canDelete(Cotisercellule $cotisercellule, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $cellule = $cotisercellule->getCellule();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $responsableZone = $cellule->getZone()->getUser();
            return $user === $responsableZone;
        }

        // Vérifier si l'utilisateur appartient à la cellule
        // Note: Pour la suppression, vous voudrez peut-être limiter à certains rôles seulement
        return $cellule->getUsers()->contains($user);
    }
}