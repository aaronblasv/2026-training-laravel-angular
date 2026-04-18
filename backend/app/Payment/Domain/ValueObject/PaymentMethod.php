<?php

declare(strict_types=1);

namespace App\Payment\Domain\ValueObject;

class PaymentMethod
{
    private const ALLOWED = ['cash', 'card', 'bizum'];

    private string $value;

    private function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new \InvalidArgumentException("Invalid payment method: $value. Allowed: " . implode(', ', self::ALLOWED));
        }
        $this->value = $value;
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public static function cash(): self { return new self('cash'); }
    public static function card(): self { return new self('card'); }
    public static function bizum(): self { return new self('bizum'); }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isCash(): bool { return $this->value === 'cash'; }
    public function isCard(): bool { return $this->value === 'card'; }
    public function isBizum(): bool { return $this->value === 'bizum'; }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
