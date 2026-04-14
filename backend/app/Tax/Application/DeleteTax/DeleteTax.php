<?php

namespace App\Tax\Application\DeleteTax;

use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

class DeleteTax
{
    public function __construct(
        private TaxRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid, int $restaurantId): void
    {
        $tax = $this->repository->findById($uuid, $restaurantId);

        if ($tax === null) {
            throw new \Exception('Tax not found');
        }

        $this->repository->delete($uuid, $restaurantId);
    }
}
