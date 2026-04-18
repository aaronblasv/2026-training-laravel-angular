<?php

declare(strict_types=1);

namespace App\Sale\Domain\ReadModel;

final readonly class SalesReportByDay
{
    public function __construct(
        public string $day,
        public int $count,
        public int $total,
    ) {}
}
