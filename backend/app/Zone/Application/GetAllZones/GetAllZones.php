<?php

namespace App\Zone\Application\GetAllZones;

use App\Zone\Domain\Entity\Zone;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;
use App\Zone\Application\GetAllZones\GetAllZonesResponse;

class GetAllZones
{
    public function __construct(
        private ZoneRepositoryInterface $repository,
    ) {}

    public function __invoke(): array
    {
        $zones = $this->repository->findAll();

        return array_map(
            fn(Zone $zone) => GetAllZonesResponse::create($zone),
            $zones
        );
    }
}