<?php

namespace App\Table\Application\DeleteTable;

use App\Table\Domain\Interfaces\TableRepositoryInterface;

class DeleteTable
{
    public function __construct(
        private TableRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid, int $restaurantId): void
    {
        $table = $this->repository->findById($uuid, $restaurantId);

        if ($table === null) {
            throw new \Exception('Table not found');
        }

        $this->repository->delete($uuid, $restaurantId);
    }
}
