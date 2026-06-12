<?php

namespace App\Security\Voter;

//use Symfony\Component\Security\Core\User\User;


use App\Entity\Detailcotisationdepartement;
//use App\Entity\Presencedepartement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DetailcotisationdepartementVoter extends Voter {

    public const EDIT = 'POST_EDIT';
    public const DETAILCOTISATIONFAMILLE_VIEW = 'cotiserdepartement_detaildepartement';

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $detaildepartement): bool {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DETAILCOTISATIONFAMILLE_VIEW]) && $detaildepartement instanceof Detailcotisationdepartement;
    }

    protected function voteOnAttribute(string $attribute, $detaildepartement, TokenInterface $token): bool {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // On va verifier si l'utilisateur est propriétaire de la seance
        if (null === $detaildepartement->getDepartementcotisation()->getDepartement()->getUser()) {
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
                return $this->canDetailcotisation($detaildepartement, $user);

                break;
        }

        return false;
    }

    private function canDetailcotisation(Detailcotisationdepartement $detaildepartement, User $user): bool {
        if ($this->security->isGranted('ROLE_SECRETAIRE')) {
            return true;
        }

        return $user === $detaildepartement->getDepartementcotisation()->getDepartement()->getUser();
    }

}
