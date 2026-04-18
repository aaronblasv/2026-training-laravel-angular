<?php

declare(strict_types=1);

namespace App\Sale\Domain\ReadModel;

final readonly class SaleSummary
{
    public function __construct(
        public string $uuid,
        public int $ticketNumber,
        public string $valueDate,
        public int $subtotal,
        public int $taxAmount,
        public int $lineDiscountTotal,
        public int $orderDiscountTotal,
        public int $total,
        public int $refundedTotal,
        public int $netTotal,
        public string $tableName,
        public string $openUserName,
        public string $closeUserName,
        public string $openedAt,
        public ?string $closedAt,
    ) {}
}
