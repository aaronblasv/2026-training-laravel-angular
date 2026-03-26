<?php

namespace App\Zone\Domain\Interfaces;

use App\Zone\Domain\Entity\Zone;

interface ZoneRepositoryInterface
{
    public function save(Zone $zone): void;
    public function findById(string $uuid): ?Zone;
    public function delete(string $id): void;
}