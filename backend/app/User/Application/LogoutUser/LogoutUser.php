<?php

namespace App\User\Application\LogoutUser;

use App\User\Domain\Interfaces\TokenGeneratorInterface;
use App\User\Domain\Entity\User;

class LogoutUser
{
    public function __construct(
        private TokenGeneratorInterface $tokenGenerator,
    ) {}

    public function __invoke(User $user)
    {
        $this->tokenGenerator->revokeTokens($user);
    }
}