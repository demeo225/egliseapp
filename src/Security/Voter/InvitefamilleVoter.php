<?php

namespace App\Security\Voter;

use App\Entity\Invitefamille;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitefamilleVoter extends Voter {

    public const SEANCEFAMILLE_EDIT = 'invitefamille_edit';
    public const SEANCEFAMILLE_VIEW = 'invitefamille_index';
    public const SEANCEFAMILLE_DELETE = 'invitefamille_delete';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $invitefamille): bool {
        return in_array($attribute, [
            self::SEANCEFAMILLE_EDIT, 
            self::SEANCEFAMILLE_VIEW, 
            self::SEANCEFAMILLE_DELETE
        ]) && $invitefamille instanceof Invitefamille;
    }

    protected function voteOnAttribute(string $attribute, $invitefamille, TokenInterface $token): bool {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // ROLE_ADMIN et ROLE_SECRETAIRE ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        // Vérifier si la séance de famille existe
        $seancefamille = $invitefamille->getSeancefamille();
        if (null === $seancefamille) {
            return false;
        }
        
        // Vérifier si la famille existe
        $famille = $seancefamille->getFamille();
        if (null === $famille) {
            return false;
        }

        // Vérifications communes
        $isUserInFamille = $this->isUserInFamille($famille, $user);
        $isFamilleResponsable = $this->isFamilleResponsable($famille, $user);
        $isZoneResponsable = $this->isZoneResponsable($famille, $user);
        $isInvitee = $this->isInvitee($invitefamille, $user);

        switch ($attribute) {
            case self::SEANCEFAMILLE_VIEW:
                return $isUserInFamille || $isFamilleResponsable || $isZoneResponsable || $isInvitee;
                
            case self::SEANCEFAMILLE_EDIT:
                return $isUserInFamille || $isFamilleResponsable || $isZoneResponsable;
                
            case self::SEANCEFAMILLE_DELETE:
                // Seul le responsable de la famille ou le responsable de zone peut supprimer
                return $isFamilleResponsable || $isZoneResponsable;
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur appartient à la famille
     */
    private function isUserInFamille($famille, User $user): bool
    {
        // Vérifier via la collection getUsers
        if (method_exists($famille, 'getUsers') && $famille->getUsers()) {
            if ($famille->getUsers()->contains($user)) {
                return true;
            }
        }
        
        // Vérifier via la collection getMembres
        if (method_exists($famille, 'getMembres') && $famille->getMembres()) {
            foreach ($famille->getMembres() as $membre) {
                if ($user === $membre) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur est responsable de la famille
     */
    private function isFamilleResponsable($famille, User $user): bool
    {
        if (!method_exists($famille, 'getUser')) {
            return false;
        }
        
        $responsable = $famille->getUser();
        if (null === $responsable) {
            return false;
        }
        
        return $user === $responsable;
    }

    /**
     * Vérifie si l'utilisateur est responsable de la zone de la famille
     */
    private function isZoneResponsable($famille, User $user): bool
    {
        // Vérifier si l'utilisateur a le rôle responsable de zone
        if (!$this->security->isGranted('ROLE_RESPONSABLE_ZONE')) {
            return false;
        }
        
        if (!method_exists($famille, 'getZone')) {
            return false;
        }
        
        $zone = $famille->getZone();
        if (null === $zone) {
            return false;
        }
        
        if (!method_exists($zone, 'getUser')) {
            return false;
        }
        
        $zoneResponsable = $zone->getUser();
        if (null === $zoneResponsable) {
            return false;
        }
        
        return $user === $zoneResponsable;
    }

    /**
     * Vérifie si l'utilisateur est l'invité lui-même
     */
    private function isInvitee(Invitefamille $invitefamille, User $user): bool
    {
        $invite = $invitefamille->getInvitefamille();
        if (null === $invite) {
            return false;
        }
        
        return $user === $invite;
    }
}