<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

// Se mantiene separado del Quantity compartido porque en pedidos la cantidad mínima válida es 1.
// Ver ADR `docs/adr/0001-refactor-plan-parked-items.md` (punto 1.8).
class Quantity
{
    private function __construct(private int $value) {}

    public static function create(int $value): self
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }
        return new self($value);
    }

    public function getValue(): int { return $this->value; }
}