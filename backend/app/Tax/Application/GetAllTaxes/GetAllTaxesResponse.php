<?php

namespace App\Tax\Application\GetAllTaxes;

use App\Tax\Domain\Entity\Tax;

final readonly class GetAllTaxesResponse
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
