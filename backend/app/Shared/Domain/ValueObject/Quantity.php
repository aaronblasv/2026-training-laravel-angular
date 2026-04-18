<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

class Quantity
{
    private int $value;

    private function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative.');
        }
        $this->value = $value;
    }

    public static function create(int $value): self
    {
        return new self($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(self $other): self
    {
        return new self(max(0, $this->value - $other->value));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
