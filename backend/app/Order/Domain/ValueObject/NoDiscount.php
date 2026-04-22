<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

final readonly class NoDiscount implements DiscountPolicy
{
    public function applyTo(Money $base): Money
    {
        return Money::zero();
    }

    public function type(): ?string
    {
        return null;
    }

    public function rawValue(): int
    {
        return 0;
    }
}
