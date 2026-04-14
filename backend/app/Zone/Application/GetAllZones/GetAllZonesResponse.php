<?php

namespace App\Zone\Application\GetAllZones;

use App\Zone\Domain\Entity\Zone;

final readonly class GetAllZonesResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
    ) {}

    public static function create(Zone $zone): self
    {
        return new self(
            $zone->uuid()->getValue(),
            $zone->name()->getValue(),
        );
    }
}
