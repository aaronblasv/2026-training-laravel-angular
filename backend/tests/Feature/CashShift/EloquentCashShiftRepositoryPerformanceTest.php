<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\CashShift\Infrastructure\Persistence\Models\EloquentCashShift;
use App\CashShift\Infrastructure\Persistence\Repositories\EloquentCashShiftRepository;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentCashShiftRepositoryPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_uuid_eager_loads_users_without_n_plus_one_queries(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B55556666',
            'email' => 'cashshift-restaurant@example.com',
            'password' => Hash::make('secret'),
        ]);

        $openedBy = EloquentUser::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Open User',
            'email' => 'open-user@example.com',
            'password' => Hash::make('secret'),
            'role' => 'admin',
            'restaurant_id' => $restaurant->id,
        ]);

        $closedBy = EloquentUser::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Close User',
            'email' => 'close-user@example.com',
            'password' => Hash::make('secret'),
            'role' => 'admin',
            'restaurant_id' => $restaurant->id,
        ]);

        $cashShift = EloquentCashShift::query()->create([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'opened_by_user_id' => $openedBy->id,
            'closed_by_user_id' => $closedBy->id,
            'status' => 'closed',
            'opening_cash' => 1000,
            'cash_total' => 1200,
            'card_total' => 200,
            'bizum_total' => 100,
            'refund_total' => 0,
            'counted_cash' => 1200,
            'cash_difference' => 0,
            'notes' => 'OK',
            'opened_at' => now()->subHour(),
            'closed_at' => now(),
        ]);

        $repository = new EloquentCashShiftRepository(new EloquentCashShift(), new EloquentUser());

        $warmup = $repository->findByUuid($restaurant->id, $cashShift->uuid);
        $this->assertNotNull($warmup);

        $queries = [];
        \DB::listen(static function (QueryExecuted $query) use (&$queries): void {
            $queries[] = strtolower(trim($query->sql));
        });

        $loaded = $repository->findByUuid($restaurant->id, $cashShift->uuid);

        $this->assertNotNull($loaded);

        $userSelects = array_values(array_filter($queries, static fn (string $sql): bool => str_starts_with($sql, 'select') && preg_match('/from\s+["`\[]?users["`\]]?/i', $sql) === 1));
        $this->assertCount(2, $userSelects);
    }
}