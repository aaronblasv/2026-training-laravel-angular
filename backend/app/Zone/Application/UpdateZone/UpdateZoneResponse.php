<?php

namespace App\Zone\Application\UpdateZone;

use App\Zone\Domain\Entity\Zone;

final readonly class UpdateZoneResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
    ) {}

    public static function create(Zone $zone): self
    {
        return new self(
            $zone->getUuid()->getValue(),
            $zone->getName()->getValue(),
        );
    }
}