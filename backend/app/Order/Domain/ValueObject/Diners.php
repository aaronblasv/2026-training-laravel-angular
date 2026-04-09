<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

class Diners
{
    private function __construct(private int $value) {}

    public static function create(int $value): self
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Diners must be at least 1.');
        }
        return new self($value);
    }

    public function getValue(): int { return $this->value; }
}