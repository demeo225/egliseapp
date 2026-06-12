<?php

namespace App\Security\Voter;

//use Symfony\Component\Security\Core\User\User;


use App\Entity\Detailcotisationzone;
//use App\Entity\Presencezone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationzoneVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONFAMILLE_VIEW = 'cotiserzone_detailzone';

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detailzone): bool {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONFAMILLE_VIEW]) && $detailzone instanceof Detailcotisationzone;
    }

    protected function voteOnAttribute(string $attribute, $detailzone, TokenInterface $token): bool {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // On va verifier si l'utilisateur est propriétaire de la seance
        if (null === $detailzone->getZonecotisation()->getZone()->getUser()) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::DETAILCOTISATIONFAMILLE_VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canDetailcotisation($detailzone, $user);

                break;
        }

        return false;
    }

    private function canDetailcotisation(Detailcotisationzone $detailzone, User $user): bool {
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        return $user === $detailzone->getZonecotisation()->getZone()->getUser();
    }

}
