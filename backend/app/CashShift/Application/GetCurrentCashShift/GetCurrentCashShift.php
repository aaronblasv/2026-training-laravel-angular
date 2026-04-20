<?php

declare(strict_types=1);

namespace App\CashShift\Application\GetCurrentCashShift;

use App\CashShift\Domain\Interfaces\CashShiftRepositoryInterface;
use App\CashShift\Domain\Interfaces\CashShiftSalesReadModelInterface;

class GetCurrentCashShift
{
    public function __construct(
        private CashShiftRepositoryInterface $repository,
        private CashShiftSalesReadModelInterface $salesReadModel,
    ) {}

    public function __invoke(int $restaurantId): ?array
    {
        $cashShift = $this->repository->findOpenByRestaurant($restaurantId);
        if (!$cashShift) {
            return null;
        }

        $summary = $this->salesReadModel->getWindowSummary($restaurantId, $cashShift->openedAt(), null);

        return [
            'uuid' => $cashShift->uuid()->getValue(),
            'status' => $cashShift->status()->value,
            'opening_cash' => $cashShift->openingCash(),
            'cash_total' => $summary->cashTotal->getValue(),
            'card_total' => $summary->cardTotal->getValue(),
            'bizum_total' => $summary->bizumTotal->getValue(),
            'refund_total' => $summary->refundTotal->getValue(),
            'expected_cash' => $summary->expectedCash(\App\Shared\Domain\ValueObject\Money::create($cashShift->openingCash()))->getValue(),
            'opened_at' => $cashShift->openedAt()->format('Y-m-d H:i:s'),
            'notes' => $cashShift->notes(),
        ];
    }
}