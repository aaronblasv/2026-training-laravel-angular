<?php

declare(strict_types=1);

namespace Tests\Unit\Order;

use App\Order\Application\GetAllOpenOrders\GetAllOpenOrders;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\Entity\OrderLine;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Domain\ValueObject\Quantity;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class GetAllOpenOrdersTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_fetches_open_order_lines_in_bulk_instead_of_per_order(): void
    {
        $restaurantId = 1;

        $firstOrder = Order::dddCreate(
            Uuid::generate(),
            $restaurantId,
            Uuid::generate(),
            Uuid::generate(),
            Diners::create(2),
        );

        $secondOrder = Order::dddCreate(
            Uuid::generate(),
            $restaurantId,
            Uuid::generate(),
            Uuid::generate(),
            Diners::create(4),
        );

        $firstLine = OrderLine::dddCreate(
            Uuid::generate(),
            $restaurantId,
            $firstOrder->uuid(),
            Uuid::generate(),
            Uuid::generate(),
            Quantity::create(1),
            1000,
            10,
        );

        $secondLine = OrderLine::dddCreate(
            Uuid::generate(),
            $restaurantId,
            $secondOrder->uuid(),
            Uuid::generate(),
            Uuid::generate(),
            Quantity::create(2),
            500,
            10,
        );

        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $repository->shouldReceive('findAllOpen')->once()->with($restaurantId)->andReturn([$firstOrder, $secondOrder]);

        $lineRepository = Mockery::mock(OrderLineRepositoryInterface::class);
        $lineRepository->shouldReceive('findAllByOrderIds')
            ->once()
            ->with([$firstOrder->uuid()->getValue(), $secondOrder->uuid()->getValue()], $restaurantId)
            ->andReturn([
                $firstOrder->uuid()->getValue() => [$firstLine],
                $secondOrder->uuid()->getValue() => [$secondLine],
            ]);
        $lineRepository->shouldNotReceive('findAllByOrderId');

        $response = (new GetAllOpenOrders($repository, $lineRepository))($restaurantId);

        $this->assertCount(2, $response);
        $this->assertSame(1100, $response[0]->total);
        $this->assertSame(1100, $response[1]->total);
    }
}
