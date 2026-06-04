<?php

namespace App\Security\Voter;

use App\Entity\Seancedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancedepartementVoter extends Voter {

    public const SEANCEDEPARTEMENT_EDIT = 'seancedepartement_edit';
    public const SEANCEDEPARTEMENT_VIEW = 'seancedepartement_index';
    public const SEANCEDEPARTEMENT_DELETE = 'seancedepartement_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancedepartement): bool {
        return in_array($attribute, [self::SEANCEDEPARTEMENT_EDIT, self::SEANCEDEPARTEMENT_VIEW, self::SEANCEDEPARTEMENT_DELETE]) 
            && $seancedepartement instanceof Seancedepartement;
    }

    protected function voteOnAttribute(string $attribute, $seancedepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_SECRETAIRE a tous les droits
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        
        // ROLE_ADMIN a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si le département existe
        $departement = $seancedepartement->getDepartement();
        if (null === $departement) {
            return false;
        }

        switch ($attribute) {
            case self::SEANCEDEPARTEMENT_EDIT:
                return $this->canEdit($seancedepartement, $user);
            case self::SEANCEDEPARTEMENT_VIEW:
                return $this->canView($seancedepartement, $user);
            case self::SEANCEDEPARTEMENT_DELETE:
                return $this->canDelete($seancedepartement, $user);
        }

        return false;
    }

    private function canEdit(Seancedepartement $seancedepartement, User $user): bool {
        $departement = $seancedepartement->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone (si le département est lié à une zone)
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $departement->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable du département (si cette notion existe)
        if (method_exists($departement, 'getUsers') && $departement->getUsers()) {
            return $user === $departement->getUsers();
        }

        // Vérifier si l'utilisateur appartient au département
        return $departement->getUsers()->contains($user);
    }

    private function canView(Seancedepartement $seancedepartement, User $user): bool {
        $departement = $seancedepartement->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $departement->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable du département
        if (method_exists($departement, 'getUsers') && $departement->getUsers()) {
            return $user === $departement->getUsers();
        }

        // Vérifier si l'utilisateur appartient au département
        return $departement->getUsers()->contains($user);
    }

    private function canDelete(Seancedepartement $seancedepartement, User $user): bool {
        $departement = $seancedepartement->getDepartement();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $departement->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable du département
        if (method_exists($departement, 'getUsers') && $departement->getUsers()) {
            return $user === $departement->getUsers();
        }

        // Pour la suppression, on peut limiter au responsable uniquement
        // Ou alors tous les membres peuvent supprimer
        // À adapter selon votre logique métier
        return $departement->getUsers()->contains($user);
    }
}