<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

final class Percentage
{
    private function __construct(private int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('Percentage must be between 0 and 100.');
        }
    }

    public static function create(int $value): self
    {
        return new self($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function applyTo(int $amount): int
    {
        return (int) round($amount * $this->value / 100);
    }
}