<?php

declare(strict_types=1);

namespace App\Dashboard\Application\GetDashboardStats;

final readonly class GetDashboardStatsResponse
{
    public function __construct(
        public array $stats,
        public array $salesThisMonth,
        public array $topProducts,
        public array $salesByDay,
    ) {}
}
