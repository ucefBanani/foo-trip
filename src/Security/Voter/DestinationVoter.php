<?php

namespace App\Security\Voter;

use App\Entity\Destination;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;


final class DestinationVoter extends Voter
{
    const CREATE = 'CREATE';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    protected function supports(string $attribute, mixed $subject): bool
    {
         
        return in_array($attribute, [self::CREATE, self::UPDATE, self::DELETE])
        && $subject instanceof Destination;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Ensure the user is logged in
        if (!$user instanceof User) {
            return false;
        }

        // Check if the user has the 'ROLE_ADMIN'
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CREATE:
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::UPDATE:
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::DELETE:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
