<?php

namespace App\Zone\Application\DeleteZone;

use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

class DeleteZone
{
    public function __construct(
        private ZoneRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid, int $restaurantId): void
    {
        $zone = $this->repository->findById($uuid, $restaurantId);

        if ($zone === null) {
            throw new \Exception('Zone not found');
        }

        $this->repository->delete($uuid, $restaurantId);
    }
}
