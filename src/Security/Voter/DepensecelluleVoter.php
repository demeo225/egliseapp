<?php

namespace App\Security\Voter;

use App\Entity\Depensecellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DepensecelluleVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const DELETE = 'depensecellule_delete';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Depensecellule;
    }

    protected function voteOnAttribute(string $attribute, $depensecellule, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ROLE_ADMIN a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ROLE_SECRETAIRE a tous les droits
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la cellule existe
        $cellule = $depensecellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($depensecellule, $user);
            case self::VIEW:
                return $this->canView($depensecellule, $user);
            case self::DELETE:
                return $this->canDelete($depensecellule, $user);
        }

        return false;
    }

    private function canEdit(Depensecellule $depensecellule, User $user): bool
    {
        $cellule = $depensecellule->getCellule();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la cellule (si cette notion existe)
        if (method_exists($cellule, 'getResponsable') && $cellule->getResponsable()) {
            return $user === $cellule->getResponsable();
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canView(Depensecellule $depensecellule, User $user): bool
    {
        $cellule = $depensecellule->getCellule();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la cellule
        if (method_exists($cellule, 'getResponsable') && $cellule->getResponsable()) {
            return $user === $cellule->getResponsable();
        }

        // Vérifier si l'utilisateur appartient à la cellule
        return $cellule->getUsers()->contains($user);
    }

    private function canDelete(Depensecellule $depensecellule, User $user): bool
    {
        $cellule = $depensecellule->getCellule();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $cellule->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la cellule
        if (method_exists($cellule, 'getResponsable') && $cellule->getResponsable()) {
            return $user === $cellule->getResponsable();
        }

        // Pour la suppression, peut-être seulement le responsable
        // Ou alors tous les membres peuvent supprimer
        // À adapter selon votre logique métier
        return $cellule->getUsers()->contains($user);
    }
}