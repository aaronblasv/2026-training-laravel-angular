<?php

namespace App\Table\Application\GetAllTables;

use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\Entity\Table;
use App\Table\Application\GetAllTables\GetAllTablesResponse;

class GetAllTables
{

    public function __construct(
        private TableRepositoryInterface $repository,
    ) {}

    public function __invoke(): array
    {
        $tables = $this->repository->findAll();

        return array_map(
            fn(Table $table) => GetAllTablesResponse::create($table),
            $tables
        );
    }
}