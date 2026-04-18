<?php

declare(strict_types=1);

namespace App\Sale\Domain\ReadModel;

final readonly class SalesReportByUser
{
    public function __construct(
        public string $userName,
        public int $count,
        public int $total,
    ) {}
}
