<?php

declare(strict_types=1);

namespace Tests\Feature\Table;

use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\Table\Infrastructure\Persistence\Repositories\EloquentTableRepository;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentTableRepositoryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_all_eager_loads_zones_without_n_plus_one_queries(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B33334444',
            'email' => 'table-restaurant@example.com',
            'password' => Hash::make('secret'),
        ]);

        $zone = EloquentZone::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Terraza',
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentTable::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Mesa 1',
            'zone_id' => $zone->id,
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentTable::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Mesa 2',
            'zone_id' => $zone->id,
            'restaurant_id' => $restaurant->id,
        ]);

        $repository = new EloquentTableRepository(new EloquentTable(), new EloquentZone());

        $warmup = $repository->findAll($restaurant->id);
        $this->assertCount(2, $warmup);

        $queries = [];
        \DB::listen(static function (QueryExecuted $query) use (&$queries): void {
            $queries[] = strtolower(trim($query->sql));
        });

        $tables = $repository->findAll($restaurant->id);

        $this->assertCount(2, $tables);

        $zoneSelects = array_values(array_filter($queries, static fn (string $sql): bool => str_starts_with($sql, 'select') && preg_match('/from\s+["`\[]?zones["`\]]?/i', $sql) === 1));
        $this->assertCount(1, $zoneSelects);
    }
}