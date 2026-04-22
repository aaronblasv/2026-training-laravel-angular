<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

enum OrderStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';
    case INVOICED = 'invoiced';

    public function isOpen(): bool
    {
        return $this === self::OPEN;
    }

    public function isClosed(): bool
    {
        return $this === self::CLOSED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isInvoiced(): bool
    {
        return $this === self::INVOICED;
    }
}
