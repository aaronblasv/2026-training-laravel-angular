<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

class Pin
{
    private string $value;

    private function __construct(string $value)
    {
        if (!preg_match('/^\d{4,6}$/', $value)) {
            throw new \InvalidArgumentException('Pin must be 4 to 6 digits.');
        }
        $this->value = $value;
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
