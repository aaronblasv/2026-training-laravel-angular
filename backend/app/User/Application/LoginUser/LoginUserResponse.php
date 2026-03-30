<?php

namespace App\User\Application\LoginUser;

final readonly class LoginUserResponse
{
    private function __construct(
        public string $token,
    ) {}

    public static function create(string $token): self
    {
        return new self(
            token: $token,
        );
    }
}