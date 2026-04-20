<?php

declare(strict_types=1);

namespace App\CashShift\Domain\ValueObject;

enum CashShiftStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';

    public function isOpen(): bool
    {
        return $this === self::OPEN;
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::OPEN => $next === self::CLOSED,
            self::CLOSED => false,
        };
    }

    public static function create(string $value): self
    {
        return self::from($value);
    }
}