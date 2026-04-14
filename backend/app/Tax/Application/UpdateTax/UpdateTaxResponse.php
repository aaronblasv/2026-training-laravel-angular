<?php

namespace App\Tax\Application\UpdateTax;

use App\Tax\Domain\Entity\Tax;

final readonly class UpdateTaxResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
        public int $percentage,
    ) {}

    public static function create(Tax $tax): self
    {
        return new self(
            $tax->uuid()->getValue(),
            $tax->name()->getValue(),
            $tax->percentage()->getValue(),
        );
    }
}
