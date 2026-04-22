<?php

declare(strict_types=1);

namespace App\Payment\Domain\ValueObject;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case BIZUM = 'bizum';

    public function isCash(): bool { return $this === self::CASH; }
    public function isCard(): bool { return $this === self::CARD; }
    public function isBizum(): bool { return $this === self::BIZUM; }

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
