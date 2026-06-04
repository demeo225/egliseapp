<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ZoneVoter extends Voter
{
//      const ZONE_UPDATE = 'zone_update';
      const ZONE_ADD = 'zone_add';
      const ZONE_DELETE = 'zone_delete';
      const ZONE_DETAIL = 'zone_detail';
      const ZONE_INDEX = 'zone_index';
      
    
    
    protected function supports(string $attribute, $zone): bool
    {
        // replace with your own logic
      
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute,[self::ZONE_ADD, self::ZONE_DELETE, self::ZONE_DETAIL, self::ZONE_INDEX])
            && $zone instanceof \App\Security\Voter\ZoneVoter;
    }

    protected function voteOnAttribute(string $attribute, $zone, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // On va vérifier ici si la zone appartient à un utilisateur
//        if(null === $zone->getUsers()) return false;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ZONE_ADD:
                // Verifie si on peut faire un ajout
                return $this->canAdd($zone, $user);
                break;
            case self::ZONE_DELETE:
                // On vérifie si on a le droit du supprimer
                return $this->canDelete($zone, $user);
                break;
           case self::ZONE_DETAIL:
                // On vérifie si on a le droit de voir le contenu
                return $this->canDetail($zone, $user);
                break;
           
                break;
            case self::ZONE_INDEX: 
                // On vérifie si on le droit de modifier
                return $this->canIndex($zone, $user);
                break;
        }

        return false;
    }
    
    private function canAdd (ZoneVoter $zone, User $user){
        return $user === $zone->getUsers();
    }
     private function canDelete(ZoneVoter $zone, User $user){
       return $user === $zone->getUsers();  
    }
    
//     private function canUpdate(ZoneVoter $zone, User $user){
//      return $user === $zone->getUsers();   
//    }
    
     private function canDetail(ZoneVoter $zone, User $user){
        return $user === $zone->getUsers(); 
    }
    
      private function canIndex(ZoneVoter $zone, User $user){
       return $user === $zone->getUsers();  
    }
}
