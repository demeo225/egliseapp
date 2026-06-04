<?php

namespace App\Security\Voter;

use App\Entity\Seancefamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SeancefamilleVoter extends Voter {

    public const SEANCEFAMILLE_EDIT = 'seancefamille_edit';
    public const SEANCEFAMILLE_VIEW = 'seancefamille_index';
    public const SEANCEFAMILLE_DELETE = 'seancefamille_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $seancefamille): bool {
        return in_array($attribute, [self::SEANCEFAMILLE_EDIT, self::SEANCEFAMILLE_VIEW, self::SEANCEFAMILLE_DELETE]) 
            && $seancefamille instanceof Seancefamille;
    }

    protected function voteOnAttribute(string $attribute, $seancefamille, TokenInterface $token): bool {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // Admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si la famille existe
        $famille = $seancefamille->getFamille();
        if (null === $famille) {
            return false;
        }

        switch ($attribute) {
            case self::SEANCEFAMILLE_EDIT:
                return $this->canEdit($seancefamille, $user);
            case self::SEANCEFAMILLE_VIEW:
                return $this->canView($seancefamille, $user);
            case self::SEANCEFAMILLE_DELETE:
                return $this->canDelete($seancefamille, $user);
        }

        return false;
    }

    private function canEdit(Seancefamille $seancefamille, User $user): bool {
        // Le secrétaire peut tout modifier
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $seancefamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la famille (si cette notion existe)
        if (method_exists($famille, 'getResponsable') && $famille->getResponsable()) {
            return $user === $famille->getResponsable();
        }

        // Vérifier si l'utilisateur appartient à la famille
        return $famille->getUsers()->contains($user);
    }

    private function canView(Seancefamille $seancefamille, User $user): bool {
        // Le secrétaire peut tout voir
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $seancefamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Vérifier si l'utilisateur est le responsable de la famille
        if (method_exists($famille, 'getResponsable') && $famille->getResponsable()) {
            return $user === $famille->getResponsable();
        }

        // Vérifier si l'utilisateur appartient à la famille
        return $famille->getUsers()->contains($user);
    }

    private function canDelete(Seancefamille $seancefamille, User $user): bool {
        // Le secrétaire peut tout supprimer
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        $famille = $seancefamille->getFamille();
        
        // Vérifier si l'utilisateur est responsable de zone
        if ($this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            $zone = $famille->getZone();
            if ($zone && $zone->getUsers()) {
                return $user === $zone->getUsers();
            }
        }

        // Pour la suppression, on peut limiter au responsable de la famille uniquement
        if (method_exists($famille, 'getResponsable') && $famille->getResponsable()) {
            return $user === $famille->getResponsable();
        }

        // Ou alors tous les membres peuvent supprimer (selon votre logique)
        return $famille->getUsers()->contains($user);
    }
}