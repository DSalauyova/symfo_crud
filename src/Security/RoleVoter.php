<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Cette class gere les roles de utilisateurs
 */
class RoleVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['ACCES_PAGES']);
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case 'ACCES_PAGES':
                return $this->canViewPage($user);
        }

        throw new \LogicException('This page is not available for your status!');
    }

    private function canViewPage(User $user): bool
    {
        return in_array('ROLE_USER', $user->getRoles()) || in_array('basic_user', $user->getRoles());
    }
}
