<?php

namespace App\Security\Voter;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UsuarioVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW])
            && $subject instanceof Usuario;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        assert($subject instanceof Usuario);

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return $this->security->isGranted('ROLE_CAMARERO') ||
                    $subject === $user;
                break;
        }

        return false;
    }
}
