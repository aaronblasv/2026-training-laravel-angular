<?php

namespace App\Table\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Domain\ValueObject\TableName;

class Table 
{
    private function __construct(
        private Uuid $uuid,
        private TableName $name,
        private string $zoneId,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        TableName $name,
        string $zoneId,
    ): self {
        return new self($uuid, $name, $zoneId);
    }

    public function dddUpdate(TableName $name, string $zoneId): void
    {
        $this->name = $name;
        $this->zoneId = $zoneId;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): TableName
    {
        return $this->name;
    }

    public function getZoneId(): string
    {
        return $this->zoneId;
    }
}