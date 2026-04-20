<?php

declare(strict_types=1);

namespace App\CashShift\Infrastructure\Persistence\ReadModels;

use App\CashShift\Domain\Interfaces\CashShiftSalesReadModelInterface;
use App\CashShift\Domain\ReadModel\CashShiftSummary;
use App\Shared\Domain\ValueObject\Money;
use Illuminate\Support\Facades\DB;

class EloquentCashShiftSalesReadModel implements CashShiftSalesReadModelInterface
{
    public function getWindowSummary(int $restaurantId, \DateTimeImmutable $from, ?\DateTimeImmutable $to): CashShiftSummary
    {
        $fromString = $from->format('Y-m-d H:i:s');
        $toString = ($to ?? new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $paymentTotals = DB::table('payments as p')
            ->join('orders as o', 'p.order_id', '=', 'o.id')
            ->where('o.restaurant_id', $restaurantId)
            ->whereBetween('p.created_at', [$fromString, $toString])
            ->select(
                DB::raw("SUM(CASE WHEN p.method = 'cash' THEN p.amount ELSE 0 END) as cash_total"),
                DB::raw("SUM(CASE WHEN p.method = 'card' THEN p.amount ELSE 0 END) as card_total"),
                DB::raw("SUM(CASE WHEN p.method = 'bizum' THEN p.amount ELSE 0 END) as bizum_total"),
            )
            ->first();

        $refundTotals = DB::table('refunds')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromString, $toString])
            ->select(
                DB::raw("SUM(CASE WHEN method = 'cash' THEN total ELSE 0 END) as cash_total"),
                DB::raw("SUM(CASE WHEN method = 'card' THEN total ELSE 0 END) as card_total"),
                DB::raw("SUM(CASE WHEN method = 'bizum' THEN total ELSE 0 END) as bizum_total"),
                DB::raw('SUM(total) as refund_total'),
            )
            ->first();

        $cashPayments = (int) ($paymentTotals->cash_total ?? 0);
        $cardPayments = (int) ($paymentTotals->card_total ?? 0);
        $bizumPayments = (int) ($paymentTotals->bizum_total ?? 0);
        $cashRefunds = (int) ($refundTotals->cash_total ?? 0);
        $cardRefunds = (int) ($refundTotals->card_total ?? 0);
        $bizumRefunds = (int) ($refundTotals->bizum_total ?? 0);

        return new CashShiftSummary(
            cashTotal: Money::create($cashPayments - $cashRefunds),
            cardTotal: Money::create($cardPayments - $cardRefunds),
            bizumTotal: Money::create($bizumPayments - $bizumRefunds),
            refundTotal: Money::create((int) ($refundTotals->refund_total ?? 0)),
        );
    }
}