<?php

namespace App\Zone\Application\CreateZone;

use App\Zone\Domain\Entity\Zone;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;
use App\Zone\Domain\ValueObject\ZoneName;
use App\Shared\Domain\ValueObject\Uuid;

final readonly class CreateZoneResponse
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