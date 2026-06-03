<?php

namespace App\Security\Voter;

use App\Entity\Detailcotisationdepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationdepartementVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONFAMILLE_VIEW = 'cotiserdepartement_detaildepartement';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detaildepartement): bool {
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONFAMILLE_VIEW]) 
            && $detaildepartement instanceof Detailcotisationdepartement;
    }

    protected function voteOnAttribute(string $attribute, $detaildepartement, TokenInterface $token): bool {
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

        // Vérifier si la cotisation département existe
        $cotisationdepartement = $detaildepartement->getCotisationdepartement();
        if (null === $cotisationdepartement) {
            return false;
        }
        
        // Vérifier si le département existe
        $departement = $cotisationdepartement->getDepartement();
        if (null === $departement) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($detaildepartement, $user);
            case self::DETAILCOTISATIONFAMILLE_VIEW:
                return $this->canViewDetail($detaildepartement, $user);
        }

        return false;
    }

    private function canEdit(Detailcotisationdepartement $detaildepartement, User $user): bool {
        $departement = $detaildepartement->getCotisationdepartement()->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone (si cette relation existe)
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $departement->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Vérifier si l'utilisateur est le responsable du département
        if ($departement->getUser() && $user === $departement->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient au département
        if (method_exists($departement, 'getUsers')) {
            return $departement->getUsers()->contains($user);
        }

        return false;
    }

    private function canViewDetail(Detailcotisationdepartement $detaildepartement, User $user): bool {
        $departement = $detaildepartement->getCotisationdepartement()->getDepartement();
        
        // Responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $departement->getZone();
            if ($zone && $zone->getUser()) {
                return $user === $zone->getUser();
            }
        }

        // Responsable du département
        if ($departement->getUser() && $user === $departement->getUser()) {
            return true;
        }

        // Vérifier si l'utilisateur appartient au département
        if (method_exists($departement, 'getUsers')) {
            return $departement->getUsers()->contains($user);
        }

        // Vérifier dans les groupes du département (si l'utilisateur est dans un groupe du département)
        foreach ($departement->getGroupes() as $groupe) {
            if ($groupe->getUsers()->contains($user)) {
                return true;
            }
        }

        return false;
    }
}