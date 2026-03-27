<?php

namespace App\Table\Application\CreateTable;

use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\ValueObject\TableName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Application\CreateTable\CreateTableResponse;

final readonly class CreateTableResponse
{
    
    private function __construct(
        public string $uuid,
        public string $name,
    ) {}

    public static function create(Table $table): self
    {
        return new self(
            $table->getUuid()->getValue(),
            $table->getName()->getValue(),
        );
    }

}