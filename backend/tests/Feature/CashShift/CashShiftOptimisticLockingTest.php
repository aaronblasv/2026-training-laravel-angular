<?php

declare(strict_types=1);

namespace Tests\Feature\CashShift;

use App\CashShift\Domain\ValueObject\ClosingCashSnapshot;
use App\CashShift\Infrastructure\Persistence\Models\EloquentCashShift;
use App\CashShift\Infrastructure\Persistence\Repositories\EloquentCashShiftRepository;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\Shared\Domain\Exception\ConcurrencyException;
use App\Shared\Domain\ValueObject\Uuid;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class CashShiftOptimisticLockingTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_uses_version_to_detect_stale_cash_shift(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B11113333',
            'email' => 'cashshift-lock@example.com',
            'password' => Hash::make('secret'),
        ]);

        $user = EloquentUser::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Cashier',
            'email' => 'cashshift-lock-user@example.com',
            'password' => Hash::make('secret'),
            'role' => 'admin',
            'restaurant_id' => $restaurant->id,
        ]);

        $cashShift = EloquentCashShift::query()->create([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'opened_by_user_id' => $user->id,
            'closed_by_user_id' => null,
            'status' => 'open',
            'opening_cash' => 1000,
            'cash_total' => 0,
            'card_total' => 0,
            'bizum_total' => 0,
            'refund_total' => 0,
            'counted_cash' => null,
            'cash_difference' => 0,
            'notes' => null,
            'opened_at' => now()->subHour(),
            'closed_at' => null,
            'version' => 1,
        ]);

        $repository = new EloquentCashShiftRepository(new EloquentCashShift(), new EloquentUser());

        $loaded = $repository->findByUuid($restaurant->id, $cashShift->uuid);
        $this->assertNotNull($loaded);
        $this->assertSame(1, $loaded->version());

        EloquentCashShift::query()->where('uuid', $cashShift->uuid)->update(['version' => 2]);

        $loaded->close(ClosingCashSnapshot::create(
            Uuid::create($user->uuid),
            500,
            0,
            0,
            0,
            1500,
            'Conteo',
        ));

        $this->expectException(ConcurrencyException::class);

        $repository->update($loaded);
    }
}