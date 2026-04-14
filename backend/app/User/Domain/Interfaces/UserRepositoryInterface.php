<?php

namespace App\User\Domain\Interfaces;

use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findAll(int $restaurantId): array;

    public function delete(User $user): void;

    public function findByPin(string $pin, int $restaurantId): ?User;
}
