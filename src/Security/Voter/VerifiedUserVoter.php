<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @extends Voter<string, mixed>
 */
class VerifiedUserVoter extends Voter
{
    public const IS_VERIFIED = 'IS_VERIFIED';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::IS_VERIFIED;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // utilisateur non connecté
        if (!$user instanceof User) {
            return false;
        }

        return $user->isVerified() && $user->isActive();
    }
}