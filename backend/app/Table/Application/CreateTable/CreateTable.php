<?php

namespace App\Table\Application\CreateTable;

use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\ValueObject\TableName;
use App\Shared\Domain\ValueObject\Uuid;


class CreateTable
{
    public function __construct(
        private TableRepositoryInterface $repository,
    ) {}

    public function __invoke(string $name, string $zoneId): CreateTableResponse
    {
        $table = Table::dddCreate(
            Uuid::generate(),
            TableName::create($name),
            $zoneId,
        );

        $this->repository->save($table);

        return CreateTableResponse::create($table);
    }
}