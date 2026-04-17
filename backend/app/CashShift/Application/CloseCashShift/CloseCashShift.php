<?php

declare(strict_types=1);

namespace App\CashShift\Application\CloseCashShift;

use App\CashShift\Domain\Exception\CashShiftNotFoundException;
use App\CashShift\Domain\Interfaces\CashShiftRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;

class CloseCashShift
{
    public function __construct(private CashShiftRepositoryInterface $repository) {}

    public function __invoke(int $restaurantId, string $cashShiftUuid, string $userUuid, int $countedCash, ?string $notes): array
    {
        $cashShift = $this->repository->findByUuid($restaurantId, $cashShiftUuid);
        if (!$cashShift || $cashShift->status() !== 'open') {
            throw new CashShiftNotFoundException($cashShiftUuid);
        }

        $summary = $this->repository->getWindowSummary($restaurantId, $cashShift->openedAt(), null);

        $cashShift->close(
            Uuid::create($userUuid),
            $summary['cash_total'],
            $summary['card_total'],
            $summary['bizum_total'],
            $summary['refund_total'],
            $countedCash,
            $notes,
        );

        $this->repository->update($cashShift);

        return [
            'uuid' => $cashShift->uuid()->getValue(),
            'status' => $cashShift->status(),
            'opening_cash' => $cashShift->openingCash(),
            'cash_total' => $cashShift->cashTotal(),
            'card_total' => $cashShift->cardTotal(),
            'bizum_total' => $cashShift->bizumTotal(),
            'refund_total' => $cashShift->refundTotal(),
            'counted_cash' => $cashShift->countedCash(),
            'cash_difference' => $cashShift->cashDifference(),
            'opened_at' => $cashShift->openedAt()->format('Y-m-d H:i:s'),
            'closed_at' => $cashShift->closedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}