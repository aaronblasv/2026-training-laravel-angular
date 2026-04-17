<?php

declare(strict_types=1);

namespace App\Restaurant\Domain\Interfaces;

interface RestaurantRepositoryInterface
{
    public function findNameById(int $restaurantId): ?string;

    public function create(string $name, string $legalName, string $taxId, string $email): int;
}

