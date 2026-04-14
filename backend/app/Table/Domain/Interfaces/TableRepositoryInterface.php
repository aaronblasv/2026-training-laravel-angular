<?php

namespace App\Table\Domain\Interfaces;

use App\Table\Domain\Entity\Table;

interface TableRepositoryInterface
{
    public function save(Table $table): void;

    public function findById(string $id, int $restaurantId): ?Table;

    public function findAll(int $restaurantId): array;

    public function delete(string $id, int $restaurantId): void;
}
