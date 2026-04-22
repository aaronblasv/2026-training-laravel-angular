<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

interface DiscountPolicy
{
    public function applyTo(Money $base): Money;

    public function type(): ?string;

    public function rawValue(): int;
}
