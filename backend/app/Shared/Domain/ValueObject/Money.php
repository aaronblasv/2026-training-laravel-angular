<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final readonly class Money
{
    private function __construct(private int $amount) {}

    public static function fromCents(int $amount): self
    {
        return new self($amount);
    }

    public static function create(int $amount): self
    {
        return self::fromCents($amount);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function getValue(): int
    {
        return $this->amount;
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    public function subtract(self $other): self
    {
        return new self($this->amount - $other->amount);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor);
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function min(self $other): self
    {
        return new self(min($this->amount, $other->amount));
    }

    public function max(self $other): self
    {
        return new self(max($this->amount, $other->amount));
    }
}
