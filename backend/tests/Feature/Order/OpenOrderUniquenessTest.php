<?php

declare(strict_types=1);

namespace Tests\Feature\Order;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Exception\TableAlreadyHasOpenOrderException;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Order\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use App\Zone\Infrastructure\Persistence\Models\EloquentZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class OpenOrderUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_translates_duplicate_open_table_constraint_to_domain_exception(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B45678901',
            'email' => 'restaurant-order@example.com',
            'password' => Hash::make('secret'),
        ]);

        $user = EloquentUser::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Waiter',
            'email' => 'waiter@example.com',
            'password' => Hash::make('secret'),
            'role' => 'waiter',
            'restaurant_id' => $restaurant->id,
        ]);

        $zone = EloquentZone::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Sala',
            'restaurant_id' => $restaurant->id,
        ]);

        $table = EloquentTable::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Mesa 1',
            'zone_id' => $zone->id,
            'restaurant_id' => $restaurant->id,
        ]);

        $repository = new EloquentOrderRepository(new EloquentOrder(), new EloquentTable(), new EloquentUser());

        $firstOrder = Order::dddCreate(
            Uuid::generate(),
            $restaurant->id,
            Uuid::create($table->uuid),
            Uuid::create($user->uuid),
            Diners::create(2),
        );

        $duplicateOrder = Order::dddCreate(
            Uuid::generate(),
            $restaurant->id,
            Uuid::create($table->uuid),
            Uuid::create($user->uuid),
            Diners::create(4),
        );

        $repository->save($firstOrder);

        $this->expectException(TableAlreadyHasOpenOrderException::class);

        $repository->save($duplicateOrder);
    }
}