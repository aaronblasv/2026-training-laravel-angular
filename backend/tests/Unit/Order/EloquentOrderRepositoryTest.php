<?php

declare(strict_types=1);

namespace Tests\Unit\Order;

use App\Order\Domain\Entity\Order;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Order\Infrastructure\Persistence\Repositories\EloquentOrderRepository;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Carbon\Carbon;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EloquentOrderRepositoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_update_accepts_carbon_updated_at_when_syncing_persisted_timestamp(): void
    {
        $model = Mockery::mock(EloquentOrder::class);
        $tableModel = Mockery::mock(EloquentTable::class);
        $userModel = Mockery::mock(EloquentUser::class);

        $repository = new EloquentOrderRepository($model, $tableModel, $userModel);

        $order = Order::dddCreate(
            Uuid::generate(),
            1,
            Uuid::generate(),
            Uuid::generate(),
            \App\Order\Domain\ValueObject\Diners::create(2),
        );

        $existingOrderQuery = Mockery::mock();
        $tableQuery = Mockery::mock();
        $updateQuery = Mockery::mock();
        $freshTimestampQuery = Mockery::mock();

        $model->shouldReceive('newQuery')->once()->andReturn($existingOrderQuery);
        $existingOrderQuery->shouldReceive('where')->once()->with('uuid', $order->uuid()->getValue())->andReturnSelf();
        $existingOrderQuery->shouldReceive('firstOrFail')->once()->andReturn(new \stdClass());

        $tableModel->shouldReceive('newQuery')->once()->andReturn($tableQuery);
        $tableQuery->shouldReceive('where')->once()->with('uuid', $order->tableId()->getValue())->andReturnSelf();
        $tableQuery->shouldReceive('firstOrFail')->once()->andReturn((object) ['id' => 42]);

        $model->shouldReceive('newQuery')->once()->andReturn($updateQuery);
        $updateQuery->shouldReceive('where')->once()->with('uuid', $order->uuid()->getValue())->andReturnSelf();
        $updateQuery->shouldReceive('when')->once()->with(false, Mockery::type(\Closure::class))->andReturnSelf();
        $updateQuery->shouldReceive('update')->once()->with(Mockery::on(
            fn (array $data) => $data['table_id'] === 42
                && $data['status'] === 'open'
                && $data['diners'] === 2
                && $data['discount_type'] === null
                && $data['discount_value'] === 0
                && $data['discount_amount'] === 0
        ))->andReturn(1);

        $model->shouldReceive('newQuery')->once()->andReturn($freshTimestampQuery);
        $freshTimestampQuery->shouldReceive('where')->once()->with('uuid', $order->uuid()->getValue())->andReturnSelf();
        $freshTimestampQuery->shouldReceive('value')->once()->with('updated_at')->andReturn(Carbon::parse('2026-04-21 10:15:00'));

        $repository->update($order);

        $this->assertNotNull($order->persistedAt());
        $this->assertSame('2026-04-21 10:15:00', $order->persistedAt()?->format('Y-m-d H:i:s'));
    }
}
