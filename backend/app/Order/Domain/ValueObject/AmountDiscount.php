<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

final readonly class AmountDiscount implements DiscountPolicy
{
    public function __construct(private int $amount) {}

    public function applyTo(Money $base): Money
    {
        return Money::fromCents(min($base->getValue(), max(0, $this->amount)));
    }

    public function type(): ?string
    {
        return DiscountType::AMOUNT->value;
    }

    public function rawValue(): int
    {
        return $this->amount;
    }
}
