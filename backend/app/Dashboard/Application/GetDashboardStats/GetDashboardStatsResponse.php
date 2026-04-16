<?php

declare(strict_types=1);

namespace App\Dashboard\Application\GetDashboardStats;

use App\Dashboard\Domain\ReadModel\DashboardStats;
use App\Dashboard\Domain\ReadModel\SaleByDay;
use App\Dashboard\Domain\ReadModel\SaleThisMonth;
use App\Dashboard\Domain\ReadModel\TopProduct;

final readonly class GetDashboardStatsResponse
{
    /**
     * @param SaleThisMonth[] $salesThisMonth
     * @param TopProduct[]    $topProducts
     * @param SaleByDay[]     $salesByDay
     */
    public function __construct(
        public DashboardStats $stats,
        public array          $salesThisMonth,
        public array          $topProducts,
        public array          $salesByDay,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'stats'            => $this->stats->toArray(),
            'sales_this_month' => array_map(fn(SaleThisMonth $s) => $s->toArray(), $this->salesThisMonth),
            'top_products'     => array_map(fn(TopProduct $p) => $p->toArray(), $this->topProducts),
            'sales_by_day'     => array_map(fn(SaleByDay $d) => $d->toArray(), $this->salesByDay),
        ];
    }
}
