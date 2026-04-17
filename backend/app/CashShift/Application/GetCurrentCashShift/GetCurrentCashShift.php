<?php

declare(strict_types=1);

namespace App\CashShift\Application\GetCurrentCashShift;

use App\CashShift\Domain\Interfaces\CashShiftRepositoryInterface;

class GetCurrentCashShift
{
    public function __construct(private CashShiftRepositoryInterface $repository) {}

    public function __invoke(int $restaurantId): ?array
    {
        $cashShift = $this->repository->findOpenByRestaurant($restaurantId);
        if (!$cashShift) {
            return null;
        }

        $summary = $this->repository->getWindowSummary($restaurantId, $cashShift->openedAt(), null);

        return [
            'uuid' => $cashShift->uuid()->getValue(),
            'status' => $cashShift->status(),
            'opening_cash' => $cashShift->openingCash(),
            'cash_total' => $summary['cash_total'],
            'card_total' => $summary['card_total'],
            'bizum_total' => $summary['bizum_total'],
            'refund_total' => $summary['refund_total'],
            'expected_cash' => $cashShift->openingCash() + $summary['cash_total'],
            'opened_at' => $cashShift->openedAt()->format('Y-m-d H:i:s'),
            'notes' => $cashShift->notes(),
        ];
    }
}