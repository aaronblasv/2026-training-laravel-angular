<?php

namespace App\Zone\Application\UpdateZone;

use App\Zone\Domain\Entity\Zone;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;
use App\Zone\Domain\ValueObject\ZoneName;
use App\Shared\Domain\ValueObject\Uuid;

class UpdateZone
{

    private ZoneRepositoryInterface $repository;

    public function __construct(ZoneRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function __invoke(string $uuid, string $name): UpdateZoneResponse
    {

        $zone = $this->repository->findById($uuid);

        if(!$zone) {
            throw new \Exception('Zone not found');
        }

        $zone->dddUpdate(ZoneName::create($name));

        $this->repository->save($zone);

        return UpdateZoneResponse::create($zone);

    }
}