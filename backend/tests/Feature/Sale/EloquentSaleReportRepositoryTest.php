<?php

declare(strict_types=1);

namespace Tests\Feature\Sale;

use App\Restaurant\Infrastructure\Persistence\Models\EloquentRestaurant;
use App\Sale\Infrastructure\Persistence\Repositories\EloquentSaleReportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentSaleReportRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_grouped_report_uses_exact_refund_totals_for_products(): void
    {
        $now = now();

        $restaurant = EloquentRestaurant::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Restaurant',
            'legal_name' => 'Restaurant SL',
            'tax_id' => 'B11223344',
            'email' => 'restaurant-report@example.com',
            'password' => Hash::make('secret'),
        ]);

        $waiterId = DB::table('users')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'name' => 'Waiter',
            'email' => 'waiter-report@example.com',
            'password' => Hash::make('secret'),
            'role' => 'waiter',
            'restaurant_id' => $restaurant->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $zoneId = DB::table('zones')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'name' => 'Sala',
            'restaurant_id' => $restaurant->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $tableId = DB::table('tables')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'zone_id' => $zoneId,
            'restaurant_id' => $restaurant->id,
            'name' => 'Mesa 1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $familyId = DB::table('families')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'name' => 'Bebidas',
            'restaurant_id' => $restaurant->id,
            'active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $taxId = DB::table('taxes')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'name' => 'IVA 10%',
            'percentage' => 10,
            'restaurant_id' => $restaurant->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $productId = DB::table('products')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'family_id' => $familyId,
            'restaurant_id' => $restaurant->id,
            'tax_id' => $taxId,
            'image_src' => null,
            'name' => 'Café',
            'price' => 500,
            'stock' => 100,
            'active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $orderId = DB::table('orders')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'status' => 'closed',
            'table_id' => $tableId,
            'opened_by_user_id' => $waiterId,
            'closed_by_user_id' => $waiterId,
            'diners' => 2,
            'opened_at' => $now,
            'closed_at' => $now,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $orderLineId = DB::table('order_lines')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'order_id' => $orderId,
            'product_id' => $productId,
            'user_id' => $waiterId,
            'quantity' => 3,
            'price' => 333,
            'tax_percentage' => 10,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $saleId = DB::table('sales')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'order_id' => $orderId,
            'user_id' => $waiterId,
            'ticket_number' => 1,
            'value_date' => $now,
            'subtotal' => 909,
            'tax_amount' => 91,
            'line_discount_total' => 0,
            'order_discount_total' => 0,
            'total' => 1000,
            'refunded_total' => 333,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $saleLineId = DB::table('sales_lines')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'sale_id' => $saleId,
            'order_line_id' => $orderLineId,
            'user_id' => $waiterId,
            'quantity' => 3,
            'price' => 333,
            'tax_percentage' => 10,
            'line_subtotal' => 909,
            'tax_amount' => 91,
            'discount_type' => null,
            'discount_value' => 0,
            'discount_amount' => 0,
            'line_total' => 1000,
            'refunded_quantity' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $refundId = DB::table('refunds')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'restaurant_id' => $restaurant->id,
            'sale_id' => $saleId,
            'user_id' => $waiterId,
            'type' => 'partial',
            'method' => 'cash',
            'reason' => 'Ajuste',
            'subtotal' => 303,
            'tax_amount' => 30,
            'total' => 333,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('refund_lines')->insert([
            'uuid' => (string) Str::uuid(),
            'refund_id' => $refundId,
            'sale_line_id' => $saleLineId,
            'quantity' => 1,
            'subtotal' => 303,
            'tax_amount' => 30,
            'total' => 333,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $report = (new EloquentSaleReportRepository())->getGroupedReport($restaurant->id, null, null);

        $this->assertCount(1, $report->byProduct);
        $this->assertSame('Café', $report->byProduct[0]->productName);
        $this->assertSame(2, $report->byProduct[0]->totalQuantity);
        $this->assertSame(667, $report->byProduct[0]->total);
    }
}