<?php

declare(strict_types=1);

namespace App\Sale\Domain\ReadModel;

final readonly class SalesReportByZone
{
    public function __construct(
        public string $zoneName,
        public int $count,
        public int $total,
    ) {}
}
