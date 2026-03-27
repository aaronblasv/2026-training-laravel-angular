<?php

namespace App\Table\Application\DeleteTable;

use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\ValueObject\TableName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Application\DeleteTable\DeleteTableResponse;

class DeleteTable
{
    public function __construct(
        private TableRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid): void
    {
        $table = $this->repository->findById($uuid);

        if (!$table) {
            throw new \Exception('Table not found');
        }

        $this->repository->delete($uuid);
    }
}