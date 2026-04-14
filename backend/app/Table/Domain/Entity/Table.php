<?php

namespace App\Table\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Domain\ValueObject\TableName;

class Table
{
    private function __construct(
        private Uuid $uuid,
        private TableName $name,
        private Uuid $zoneId,
        private int $restaurantId,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        TableName $name,
        Uuid $zoneId,
        int $restaurantId,
    ): self {
        return new self($uuid, $name, $zoneId, $restaurantId);
    }

    public static function fromPersistence(
        string $uuid,
        string $name,
        string $zoneId,
        int $restaurantId,
    ): self {
        return new self(
            Uuid::create($uuid),
            TableName::create($name),
            Uuid::create($zoneId),
            $restaurantId,
        );
    }

    public function dddUpdate(TableName $name, Uuid $zoneId): void
    {
        $this->name = $name;
        $this->zoneId = $zoneId;
    }

    public function uuid(): Uuid
    {
        return $this->uuid;
    }

    public function name(): TableName
    {
        return $this->name;
    }

    public function zoneId(): Uuid
    {
        return $this->zoneId;
    }

    public function restaurantId(): int
    {
        return $this->restaurantId;
    }
}
