<?php

declare(strict_types=1);

namespace Tests\Unit\Table;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Entity\OrderLine;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Domain\ValueObject\Quantity;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Application\MergeTables\MergeTables;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\ValueObject\TableName;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class MergeTablesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_recomputes_survivor_discount_from_current_lines(): void
    {
        $restaurantId = 1;
        $parentTableUuid = Uuid::generate()->getValue();
        $orderUuid = Uuid::generate()->getValue();

        $table = Table::dddCreate(
            Uuid::create($parentTableUuid),
            TableName::create('Mesa principal'),
            Uuid::generate(),
            $restaurantId,
        );

        $survivorOrder = Order::fromPersistence(
            $orderUuid,
            $restaurantId,
            'open',
            $parentTableUuid,
            Uuid::generate()->getValue(),
            null,
            2,
            'percentage',
            10,
            0,
            new \DateTimeImmutable('2026-04-27 10:00:00'),
            null,
            new \DateTimeImmutable('2026-04-27 10:00:00'),
        );

        $line = OrderLine::dddCreate(
            Uuid::generate(),
            $restaurantId,
            Uuid::create($orderUuid),
            Uuid::generate(),
            Uuid::generate(),
            Quantity::create(1),
            1000,
            10,
        );

        $tableRepository = Mockery::mock(TableRepositoryInterface::class);
        $tableRepository->shouldReceive('findById')->once()->with($parentTableUuid, $restaurantId)->andReturn($table);
        $tableRepository->shouldNotReceive('update');

        $orderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $orderRepository->shouldReceive('findOpenByTableId')->twice()->with($parentTableUuid, $restaurantId)->andReturn($survivorOrder);
        $orderRepository->shouldReceive('update')->once()->with(Mockery::on(
            fn (Order $updatedOrder): bool => $updatedOrder->discountAmount() === 100
                && $updatedOrder->discountType() === 'amount'
                && $updatedOrder->discountValue() === 100
        ));
        $orderRepository->shouldNotReceive('delete');

        $lineRepository = Mockery::mock(OrderLineRepositoryInterface::class);
        $lineRepository->shouldReceive('findAllByOrderId')->twice()->with($orderUuid, $restaurantId)->andReturn([$line]);
        $lineRepository->shouldNotReceive('update');

        $useCase = new MergeTables(
            $tableRepository,
            $orderRepository,
            $lineRepository,
            new class implements TransactionManagerInterface {
                public function run(callable $callback): mixed
                {
                    return $callback();
                }
            },
        );

        $useCase($parentTableUuid, [], $restaurantId);
    }
}