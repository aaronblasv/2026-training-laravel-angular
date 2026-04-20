<?php

declare(strict_types=1);

namespace App\CashShift\Domain\ReadModel;

use App\Shared\Domain\ValueObject\Money;

final readonly class CashShiftSummary
{
    public function __construct(
        public Money $cashTotal,
        public Money $cardTotal,
        public Money $bizumTotal,
        public Money $refundTotal,
    ) {}

    public function expectedCash(Money $openingCash): Money
    {
        return $openingCash->add($this->cashTotal);
    }

    /** @return array{cash_total:int,card_total:int,bizum_total:int,refund_total:int} */
    public function toArray(): array
    {
        return [
            'cash_total' => $this->cashTotal->getValue(),
            'card_total' => $this->cardTotal->getValue(),
            'bizum_total' => $this->bizumTotal->getValue(),
            'refund_total' => $this->refundTotal->getValue(),
        ];
    }
}