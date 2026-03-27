<?php

namespace App\Table\Application\UpdateTable;

use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\ValueObject\TableName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Application\UpdateTable\UpdateTableResponse;

class UpdateTable
{
    public function __construct(
        private TableRepositoryInterface $repository,
    ) {}

    public function __invoke(string $uuid, string $name): UpdateTableResponse
    {
        $table = $this->repository->findById($uuid);

        if (!$table) {
            throw new \Exception('Table not found');
        }

        $table->dddUpdate(TableName::create($name), $table->getZoneId());

        $this->repository->save($table);

        return UpdateTableResponse::create($table);
    }
}