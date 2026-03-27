<?php

namespace App\Table\Application\GetAllTables;

use App\Table\Domain\Entity\Table;

final readonly class GetAllTablesResponse
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