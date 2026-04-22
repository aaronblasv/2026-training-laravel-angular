<?php

declare(strict_types=1);

namespace Tests\Unit\Order;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Shared\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_from_persistence_hydrates_native_enum(): void
    {
        $order = Order::fromPersistence(
            (string) Uuid::generate()->getValue(),
            1,
            'closed',
            Uuid::generate()->getValue(),
            Uuid::generate()->getValue(),
            Uuid::generate()->getValue(),
            2,
            null,
            0,
            0,
            new \DateTimeImmutable('2026-04-22 10:00:00'),
            new \DateTimeImmutable('2026-04-22 10:30:00'),
            null,
        );

        $this->assertSame(OrderStatus::CLOSED, $order->status());
        $this->assertSame('closed', $order->status()->value);
    }

    public function test_order_transitions_keep_expected_status_values(): void
    {
        $order = Order::dddCreate(
            Uuid::generate(),
            1,
            Uuid::generate(),
            Uuid::generate(),
            Diners::create(2),
        );

        $this->assertSame(OrderStatus::OPEN, $order->status());
        $this->assertTrue($order->status()->isOpen());

        $order->close(Uuid::generate());

        $this->assertSame(OrderStatus::CLOSED, $order->status());
        $this->assertTrue($order->status()->isClosed());
        $this->assertSame('closed', $order->status()->value);
    }
}