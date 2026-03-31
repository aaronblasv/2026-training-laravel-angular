<?php

namespace App\User\Application\UpdateUser;

use App\User\Domain\Entity\User;

final readonly class UpdateUserResponse
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
            name: $user->name(),
            email: $user->email()->getValue(),
        );
    }
}