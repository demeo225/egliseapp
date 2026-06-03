<?php

namespace App\Security\Voter;

use App\Entity\Detailcotisationcellule;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationcelluleVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONCELLULE_VIEW = 'cotisercellule_detailcellule';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detailcellule): bool {
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONCELLULE_VIEW]) 
            && $detailcellule instanceof Detailcotisationcellule;
    }

    protected function voteOnAttribute(string $attribute, $detailcellule, TokenInterface $token): bool {
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

        // Vérifier si la cotisation cellule existe
        $cotisationcellule = $detailcellule->getCotisationcellule();
        if (null === $cotisationcellule) {
            return false;
        }
        
        // Vérifier si la cellule existe
        $cellule = $cotisationcellule->getCellule();
        if (null === $cellule) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($detailcellule, $user);
            case self::DETAILCOTISATIONCELLULE_VIEW:
                return $this->canViewDetail($detailcellule, $user);
        }

        return false;
    }

    private function canEdit(Detailcotisationcellule $detailcellule, User $user): bool {
        $cellule = $detailcellule->getCotisationcellule()->getCellule();
        
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

    private function canViewDetail(Detailcotisationcellule $detailcellule, User $user): bool {
        $cellule = $detailcellule->getCotisationcellule()->getCellule();
        
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
}