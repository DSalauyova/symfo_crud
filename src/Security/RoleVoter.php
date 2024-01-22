<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Cette class gere autorisation basée sur les roles des utilisateurs
 */
class RoleVoter extends Voter
{
    /**determine si voter 
     * en param attribut est une chaine de car 'ACCES_PAGES' qui nomme le droit,
     * $subject est en objet par rapport à qui ce droit est verifié
     * si l'attribut est ACCES_PAGES, il retourne true (ctd Voter est utilisé pour decider l'acces) */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['ACCES_PAGES']);
    }

    /**décider si l'accès doit être accordé ou non 
     * en param $token - user authentifié et son role et recup son role
     * evaluer l'attribut, si c'est ACCES-Pages, on permet de voir la Page(methode VieuwP)
     */
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
    //implémente la logique pour déterminer ce que c'est l'acces aux pages, soit si le role de cet utilisateur courrant est Role-user ou basic-user, il returne true (acces accordé)
    private function canViewPage(User $user): bool
    {
        return in_array('ROLE_USER', $user->getRoles()) || in_array('basic_user', $user->getRoles());
    }
}
