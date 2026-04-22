<?php

declare(strict_types=1);

namespace Tests\Feature\Sale;

use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Order\Infrastructure\Persistence\Models\EloquentOrderLine;
use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\Sale\Infrastructure\Persistence\Models\EloquentSale;
use App\Sale\Infrastructure\Persistence\Models\EloquentSaleLine;
use App\Sale\Infrastructure\Persistence\Repositories\EloquentSaleWriteRepository;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketCounterRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_next_ticket_number_continues_after_existing_sales(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B12345678',
            'email' => 'restaurant@example.com',
            'password' => Hash::make('secret'),
        ]);

        DB::table('restaurant_ticket_counters')->insert([
            'restaurant_id' => $restaurant->id,
            'last_ticket_number' => 7,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $repository = new EloquentSaleWriteRepository(
            new EloquentSale(),
            new EloquentSaleLine(),
            new EloquentOrder(),
            new EloquentOrderLine(),
            new EloquentUser(),
        );

        $first = $repository->getNextTicketNumber($restaurant->id);
        $second = $repository->getNextTicketNumber($restaurant->id);

        $this->assertSame(8, $first);
        $this->assertSame(9, $second);
        $this->assertDatabaseHas('restaurant_ticket_counters', [
            'restaurant_id' => $restaurant->id,
            'last_ticket_number' => 9,
        ]);
    }

    public function test_get_next_ticket_number_bootstraps_counter_for_restaurants_without_sales(): void
    {
        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B87654321',
            'email' => 'restaurant2@example.com',
            'password' => Hash::make('secret'),
        ]);

        $repository = new EloquentSaleWriteRepository(
            new EloquentSale(),
            new EloquentSaleLine(),
            new EloquentOrder(),
            new EloquentOrderLine(),
            new EloquentUser(),
        );

        $ticketNumber = $repository->getNextTicketNumber($restaurant->id);

        $this->assertSame(1, $ticketNumber);
        $this->assertDatabaseHas('restaurant_ticket_counters', [
            'restaurant_id' => $restaurant->id,
            'last_ticket_number' => 1,
        ]);
    }
}