<?php

declare(strict_types=1);

namespace App\Invoice\Infrastructure\Persistence\Providers;

use App\Invoice\Domain\Interfaces\InvoiceOrderDataProviderInterface;
use App\Invoice\Domain\ReadModel\OrderForInvoice;
use App\Invoice\Domain\ReadModel\OrderLineForInvoice;
use Illuminate\Support\Facades\DB;

class EloquentInvoiceOrderDataProvider implements InvoiceOrderDataProviderInterface
{
    public function getOrderForInvoice(string $orderUuid, int $restaurantId): ?OrderForInvoice
    {
        $order = DB::table('orders')
            ->where('uuid', $orderUuid)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if (!$order) {
            return null;
        }

        $lines = DB::table('order_lines')
            ->where('order_id', $order->id)
            ->where('restaurant_id', $restaurantId)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($row) => new OrderLineForInvoice(
                uuid: $row->uuid,
                quantity: (int) $row->quantity,
                price: (int) $row->price,
                taxPercentage: (int) $row->tax_percentage,
                discountType: $row->discount_type,
                discountValue: (int) $row->discount_value,
                discountAmount: (int) $row->discount_amount,
            ))
            ->all();

        $linesSubtotal = array_reduce($lines, fn(int $c, OrderLineForInvoice $l) => $c + $l->subtotalAfterDiscount(), 0);
        $taxAmount = array_reduce($lines, fn(int $c, OrderLineForInvoice $l) => $c + $l->taxAmount(), 0);

        // Apply order-level discount
        $orderDiscountAmount = 0;
        if ($order->discount_type !== null && $order->discount_value > 0) {
            $orderDiscountAmount = $order->discount_type === 'percentage'
                ? (int) round($linesSubtotal * $order->discount_value / 100)
                : min($linesSubtotal, (int) $order->discount_value);
        }

        $subtotal = max(0, $linesSubtotal - $orderDiscountAmount);

        // Adjust tax proportionally if order discount applies
        if ($linesSubtotal > 0 && $orderDiscountAmount > 0) {
            $ratio = $subtotal / $linesSubtotal;
            $taxAmount = (int) round($taxAmount * $ratio);
        }

        return new OrderForInvoice(
            orderUuid: $orderUuid,
            subtotal: $subtotal,
            taxAmount: $taxAmount,
            total: $subtotal + $taxAmount,
            lines: $lines,
        );
    }
}
