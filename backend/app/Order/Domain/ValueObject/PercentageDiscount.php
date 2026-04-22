<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

final readonly class PercentageDiscount implements DiscountPolicy
{
    public function __construct(private int $percentage) {}

    public function applyTo(Money $base): Money
    {
        $rawAmount = Percentage::create($this->percentage)->applyTo($base->getValue());

        return Money::fromCents(min($base->getValue(), max(0, $rawAmount)));
    }

    public function type(): ?string
    {
        return DiscountType::PERCENTAGE->value;
    }

    public function rawValue(): int
    {
        return $this->percentage;
    }
}
