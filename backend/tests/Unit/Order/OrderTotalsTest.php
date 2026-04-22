<?php

declare(strict_types=1);

namespace Tests\Unit\Order;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Entity\OrderLine;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Domain\ValueObject\Quantity;
use App\Shared\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class OrderTotalsTest extends TestCase
{
    public function test_compute_totals_aggregates_discounts_and_tax_in_single_pass(): void
    {
        $order = Order::dddCreate(
            Uuid::generate(),
            1,
            Uuid::generate(),
            Uuid::generate(),
            Diners::create(2),
        );

        $order->applyDiscount('amount', 300, 1800);

        $lines = [
            OrderLine::dddCreate(
                Uuid::generate(),
                1,
                $order->uuid(),
                Uuid::generate(),
                Uuid::generate(),
                Quantity::create(2),
                500,
                10,
            ),
            OrderLine::dddCreate(
                Uuid::generate(),
                1,
                $order->uuid(),
                Uuid::generate(),
                Uuid::generate(),
                Quantity::create(1),
                1000,
                10,
                'percentage',
                20,
            ),
        ];

        $totals = $order->computeTotals($lines);

        $this->assertSame(1500, $totals->subtotal->getValue());
        $this->assertSame(200, $totals->lineDiscounts->getValue());
        $this->assertSame(300, $totals->orderDiscount->getValue());
        $this->assertSame(150, $totals->taxAmount->getValue());
        $this->assertSame(1650, $totals->total->getValue());
    }

    public function test_legacy_total_helpers_delegate_to_computed_totals(): void
    {
        $order = Order::dddCreate(
            Uuid::generate(),
            1,
            Uuid::generate(),
            Uuid::generate(),
            Diners::create(2),
        );

        $line = OrderLine::dddCreate(
            Uuid::generate(),
            1,
            $order->uuid(),
            Uuid::generate(),
            Uuid::generate(),
            Quantity::create(2),
            500,
            10,
            'amount',
            100,
        );

        $lines = [$line];

        $totals = $order->computeTotals($lines);

        $this->assertSame($totals->subtotal->getValue(), $order->calculateSubtotal($lines));
        $this->assertSame($totals->taxAmount->getValue(), $order->calculateTaxAmount($lines));
        $this->assertSame($totals->lineDiscounts->getValue(), $order->calculateLineDiscountTotal($lines));
        $this->assertSame($totals->orderDiscount->getValue(), $order->calculateOrderDiscountAmount($lines));
    }
}
