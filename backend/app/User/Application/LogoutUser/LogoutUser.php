<?php

declare(strict_types=1);

namespace App\User\Application\LogoutUser;

use App\User\Domain\Interfaces\TokenGeneratorInterface;

class LogoutUser
{
    public function __construct(
        private TokenGeneratorInterface $tokenGenerator,
    ) {}

    public function __invoke(string $userUuid): void
    {
        $this->tokenGenerator->revokeTokensByUuid($userUuid);
    }
}