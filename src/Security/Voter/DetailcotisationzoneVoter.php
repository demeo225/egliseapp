<?php

namespace App\Security\Voter;

use App\Entity\Detailcotisationzone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationzoneVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONFAMILLE_VIEW = 'cotiserzone_detailzone';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detailzone): bool {
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONFAMILLE_VIEW]) 
            && $detailzone instanceof Detailcotisationzone;
    }

    protected function voteOnAttribute(string $attribute, $detailzone, TokenInterface $token): bool {
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

        // Vérifier si la zonecotisation existe
        $zonecotisation = $detailzone->getZonecotisation();
        if (null === $zonecotisation) {
            return false;
        }
        
        // Vérifier si la zone existe
        $zone = $zonecotisation->getZone();
        if (null === $zone) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($detailzone, $user);
            case self::DETAILCOTISATIONFAMILLE_VIEW:
                return $this->canViewDetail($detailzone, $user);
        }

        return false;
    }

    private function canEdit(Detailcotisationzone $detailzone, User $user): bool {
        $zone = $detailzone->getZonecotisation()->getZone();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $zone->getUser() && $user === $zone->getUser();
        }
        
        // Vérifier si l'utilisateur appartient à la zone (via les cellules)
        foreach ($zone->getCellules() as $cellule) {
            if ($cellule->getUsers()->contains($user)) {
                return true;
            }
        }
        
        // Si la zone a une collection d'utilisateurs directe
        if (method_exists($zone, 'getUsers')) {
            return $zone->getUsers()->contains($user);
        }
        
        return false;
    }

    private function canViewDetail(Detailcotisationzone $detailzone, User $user): bool {
        $zone = $detailzone->getZonecotisation()->getZone();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return $zone->getUser() && $user === $zone->getUser();
        }
        
        // Vérifier si l'utilisateur appartient à la zone
        foreach ($zone->getCellules() as $cellule) {
            if ($cellule->getUsers()->contains($user)) {
                return true;
            }
        }
        
        // Si la zone a une collection d'utilisateurs directe
        if (method_exists($zone, 'getUsers')) {
            return $zone->getUsers()->contains($user);
        }
        
        return false;
    }
}