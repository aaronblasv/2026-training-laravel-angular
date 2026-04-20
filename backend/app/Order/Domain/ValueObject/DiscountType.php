<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

enum DiscountType: string
{
    case AMOUNT = 'amount';
    case PERCENTAGE = 'percentage';

    public static function create(?string $value): ?self
    {
        return $value === null ? null : self::from($value);
    }

    public function calculateAmount(int $baseAmount, int $discountValue): int
    {
        $rawAmount = match ($this) {
            self::PERCENTAGE => Percentage::create($discountValue)->applyTo($baseAmount),
            self::AMOUNT => $discountValue,
        };

        return max(0, min($baseAmount, $rawAmount));
    }
}