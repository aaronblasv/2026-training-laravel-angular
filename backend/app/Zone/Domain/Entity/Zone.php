<?php

namespace App\Zone\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use App\Zone\Domain\ValueObject\ZoneName;

class Zone 
{
    private function __construct(
        private Uuid $uuid,
        private ZoneName $name,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        ZoneName $name,
    ): self {
        return new self($uuid, $name);
    }

    public function dddUpdate(ZoneName $name): void
    {
        $this->name = $name;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getName(): ZoneName
    {
        return $this->name;
    }
}