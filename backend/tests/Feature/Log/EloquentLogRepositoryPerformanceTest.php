<?php

declare(strict_types=1);

namespace Tests\Feature\Log;

use App\Log\Infrastructure\Persistence\Models\EloquentLog;
use App\Log\Infrastructure\Persistence\Repositories\EloquentLogRepository;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentLogRepositoryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_all_eager_loads_users_without_n_plus_one_queries(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B11112222',
            'email' => 'log-restaurant@example.com',
            'password' => Hash::make('secret'),
        ]);

        $user = EloquentUser::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Auditor',
            'email' => 'log-user@example.com',
            'password' => Hash::make('secret'),
            'role' => 'admin',
            'restaurant_id' => $restaurant->id,
        ]);

        EloquentLog::query()->create([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->uuid,
            'action' => 'order.created',
            'entity_type' => 'order',
            'entity_uuid' => (string) Str::uuid(),
            'data' => ['ok' => true],
            'ip_address' => '127.0.0.1',
        ]);

        EloquentLog::query()->create([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->uuid,
            'action' => 'order.closed',
            'entity_type' => 'order',
            'entity_uuid' => (string) Str::uuid(),
            'data' => ['ok' => true],
            'ip_address' => '127.0.0.1',
        ]);

        $repository = new EloquentLogRepository(new EloquentLog(), new EloquentUser());

        $warmup = $repository->findAll($restaurant->id);
        $this->assertCount(2, $warmup);

        $queries = [];
        \DB::listen(static function (QueryExecuted $query) use (&$queries): void {
            $queries[] = strtolower(trim($query->sql));
        });

        $logs = $repository->findAll($restaurant->id);

        $this->assertCount(2, $logs);
        $this->assertSame($user->uuid, $logs[0]->userId());

        $userSelects = array_values(array_filter($queries, static fn (string $sql): bool => str_starts_with($sql, 'select') && preg_match('/from\s+["`\[]?users["`\]]?/i', $sql) === 1));
        $this->assertCount(1, $userSelects);
    }
}