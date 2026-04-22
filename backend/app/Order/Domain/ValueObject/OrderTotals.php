<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

final readonly class OrderTotals
{
    public function __construct(
        public Money $subtotal,
        public Money $lineDiscounts,
        public Money $orderDiscount,
        public Money $taxAmount,
        public Money $total,
    ) {}
}
