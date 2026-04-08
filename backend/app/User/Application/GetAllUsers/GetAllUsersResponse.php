<?php

namespace App\User\Application\GetAllUsers;

use App\User\Domain\Entity\User;

final readonly class GetAllUsersResponse
{
    private function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $role,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function create(User $user): self
    {
        return new self(
            id: $user->id()->getValue(),
            name: $user->name(),
            email: $user->email()->getValue(),
            role: $user->role()->getValue(),
            createdAt: $user->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $user->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}