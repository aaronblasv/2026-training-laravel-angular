<?php

declare(strict_types=1);

namespace App\Restaurant\Application\CreateRestaurant;

final readonly class CreateRestaurantResponse
{
    public function __construct(
        public int $restaurantId,
        public string $restaurantName,
        public string $adminUuid,
        public string $adminEmail,
    ) {}
}
