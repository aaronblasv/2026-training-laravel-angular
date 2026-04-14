<?php

namespace App\User\Application\GetUserById;

use App\User\Domain\Entity\User;

final readonly class GetUserByIdResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
        public string $email,
    ) {}

    public static function create(User $user): self
    {
        return new self(
            uuid: $user->id()->getValue(),
            name: $user->name()->getValue(),
            email: $user->email()->getValue(),
        );
    }
}