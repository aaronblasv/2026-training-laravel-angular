<?php

declare(strict_types=1);

namespace App\Dashboard\Domain\ReadModel;

final readonly class TopProduct
{
    public function __construct(
        public string $name,
        public int    $totalQuantity,
        public int    $totalRevenue,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'          => $this->name,
            'total_quantity' => $this->totalQuantity,
            'total_revenue'  => $this->totalRevenue,
        ];
    }
}
