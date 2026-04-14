<?php

namespace App\Table\Infrastructure\Persistence\Repositories;

use App\Table\Domain\Entity\Table;
use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;

class EloquentTableRepository implements TableRepositoryInterface
{
    public function __construct(
        private EloquentTable $model,
        private EloquentZone $zoneModel,
    ) {}

    public function findAll(int $restaurantId): array
    {
        return $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->get()
            ->map(fn(EloquentTable $table) => $this->toDomain($table))
            ->toArray();
    }

    public function save(Table $table): void
    {
        $zone = $this->zoneModel->newQuery()
            ->where('uuid', $table->zoneId()->getValue())
            ->firstOrFail();

        $this->model->newQuery()->updateOrCreate(
            ['uuid' => $table->uuid()->getValue()],
            [
                'name' => $table->name()->getValue(),
                'zone_id' => $zone->id,
                'restaurant_id' => $table->restaurantId(),
            ]
        );
    }

    public function findById(string $id, int $restaurantId): ?Table
    {
        $table = $this->model->newQuery()
            ->where('uuid', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        return $table ? $this->toDomain($table) : null;
    }

    public function delete(string $id, int $restaurantId): void
    {
        $this->model->newQuery()
            ->where('uuid', $id)
            ->where('restaurant_id', $restaurantId)
            ->delete();
    }

    private function toDomain(EloquentTable $table): Table
    {
        $zone = $this->zoneModel->newQuery()->find($table->zone_id);

        return Table::fromPersistence(
            $table->uuid,
            $table->name,
            $zone->uuid,
            $table->restaurant_id,
        );
    }
}
