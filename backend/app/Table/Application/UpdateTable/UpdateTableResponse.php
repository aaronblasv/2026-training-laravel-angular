<?php

namespace App\Table\Application\UpdateTable;

use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\ValueObject\TableName;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Application\UpdateTable\UpdateTableResponse;

final readonly class UpdateTableResponse
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