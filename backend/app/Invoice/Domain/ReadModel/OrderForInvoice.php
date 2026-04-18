<?php

declare(strict_types=1);

namespace App\Invoice\Domain\ReadModel;

final readonly class OrderForInvoice
{
    /**
     * @param OrderLineForInvoice[] $lines
     */
    public function __construct(
        public string $orderUuid,
        public int $subtotal,
        public int $taxAmount,
        public int $total,
        public array $lines,
    ) {}
}
