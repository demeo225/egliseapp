<?php

namespace App\Security\Voter;

use App\Entity\Presencezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
//use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

class PresencezoneVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const PRESENCEFAMILLE_VIEW = 'seancezone_presence';

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $presencezone): bool {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::PRESENCEFAMILLE_VIEW]) && $presencezone instanceof Presencezone;
    }

    protected function voteOnAttribute(string $attribute, $presencezone, TokenInterface $token): bool {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }
        // On va verifier si l'utilisateur est propriétaire de la seance
        if (null === $presencezone->getZone()->getUser()) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::PRESENCEFAMILLE_VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canListeparticipant($presencezone, $user);

                break;
        }

        return false;
    }

    private function canListeparticipant(Presencezone $presencezone, User $user): bool {

        return $user === $presencezone->getZone()->getUser();
    }

}
