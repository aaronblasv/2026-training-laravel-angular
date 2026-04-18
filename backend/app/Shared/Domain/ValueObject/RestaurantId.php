<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

class RestaurantId
{
    private int $value;

    private function __construct(int $value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException("Restaurant ID must be positive, got: $value");
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

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
