<?php

declare(strict_types=1);

namespace App\Sale\Domain\ReadModel;

final readonly class SalesReportByProduct
{
    public function __construct(
        public string $productName,
        public int $totalQuantity,
        public int $total,
    ) {}
}
