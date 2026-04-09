<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

class OrderStatus
{
    private const OPEN = 'open';
    private const CLOSED = 'closed';
    private const VALID_STATUSES = [self::OPEN, self::CLOSED];

    private function __construct(private string $value) {}

    public static function open(): self
    {
        return new self(self::OPEN);
    }

    public static function closed(): self
    {
        return new self(self::CLOSED);
    }

    public static function create(string $value): self
    {
        if (!in_array($value, self::VALID_STATUSES)) {
            throw new \InvalidArgumentException("Invalid order status: {$value}");
        }
        return new self($value);
    }

    public function isOpen(): bool
    {
        return $this->value === self::OPEN;
    }

    public function getValue(): string { return $this->value; }
}