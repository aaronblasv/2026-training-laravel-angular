<?php

declare(strict_types=1);

namespace Tests\Unit\Order;

use App\Order\Application\SendOrderToKitchen\SendOrderToKitchen;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\Diners;
use App\Shared\Application\Context\AuditContext;
use App\Shared\Domain\Event\ActionLogged;
use App\Shared\Domain\Interfaces\DomainEventBusInterface;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\Uuid;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class SendOrderToKitchenTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_marks_pending_lines_with_single_bulk_update(): void
    {
        $restaurantId = 1;
        $orderUuid = Uuid::generate()->getValue();
        $auditContext = new AuditContext($restaurantId, Uuid::generate()->getValue(), '127.0.0.1');

        $order = Order::dddCreate(
            Uuid::create($orderUuid),
            $restaurantId,
            Uuid::generate(),
            Uuid::generate(),
            Diners::create(2),
        );

        $firstLineUuid = Uuid::generate()->getValue();
        $secondLineUuid = Uuid::generate()->getValue();

        $pendingLineA = Mockery::mock();
        $pendingLineA->shouldReceive('isSentToKitchen')->andReturn(false);
        $pendingLineA->shouldReceive('uuid->getValue')->andReturn($firstLineUuid);

        $pendingLineB = Mockery::mock();
        $pendingLineB->shouldReceive('isSentToKitchen')->andReturn(false);
        $pendingLineB->shouldReceive('uuid->getValue')->andReturn($secondLineUuid);

        $alreadySentLine = Mockery::mock();
        $alreadySentLine->shouldReceive('isSentToKitchen')->andReturn(true);

        $orderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $orderRepository->shouldReceive('findById')->once()->with($orderUuid, $restaurantId)->andReturn($order);

        $lineRepository = Mockery::mock(OrderLineRepositoryInterface::class);
        $lineRepository->shouldReceive('findAllByOrderId')->once()->with($orderUuid, $restaurantId)->andReturn([$pendingLineA, $pendingLineB, $alreadySentLine]);
        $lineRepository->shouldReceive('bulkMarkSentToKitchen')
            ->once()
            ->with([$firstLineUuid, $secondLineUuid], $restaurantId, Mockery::type(DomainDateTime::class));
        $lineRepository->shouldNotReceive('update');

        $domainEventBus = Mockery::mock(DomainEventBusInterface::class);
        $domainEventBus->shouldReceive('dispatch')->once()->withArgs(function (...$events) use ($orderUuid, $auditContext) {
            foreach ($events as $event) {
                if ($event instanceof ActionLogged) {
                    return $event->action === 'order.sent_to_kitchen'
                        && $event->entityUuid === $orderUuid
                        && $event->userId === $auditContext->userId
                        && $event->data === ['lines_sent' => 2];
                }
            }

            return false;
        });

        $useCase = new SendOrderToKitchen(
            $orderRepository,
            $lineRepository,
            $this->transactionManager(),
            $domainEventBus,
        );

        $useCase($auditContext, $orderUuid);
    }

    private function transactionManager(): TransactionManagerInterface
    {
        return new class implements TransactionManagerInterface {
            public function run(callable $callback): mixed
            {
                return $callback();
            }
        };
    }
}