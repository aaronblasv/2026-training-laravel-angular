<?php

namespace App\User\Application\LoginUser;

use App\User\Domain\Entity\User;

final readonly class LoginUserResponse
{
    private function __construct(
        public string $token,
        public string $role,
    ) {}

    public static function create(string $token, User $user): self
    {
        return new self(
            token: $token,
            role: $user->role()->getValue(),
        );
    }
}