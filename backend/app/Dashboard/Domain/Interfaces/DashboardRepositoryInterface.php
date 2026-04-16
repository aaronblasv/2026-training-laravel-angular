<?php

declare(strict_types=1);

namespace App\Dashboard\Domain\Interfaces;

use App\Dashboard\Domain\ReadModel\DashboardStats;
use App\Dashboard\Domain\ReadModel\SaleByDay;
use App\Dashboard\Domain\ReadModel\SaleThisMonth;
use App\Dashboard\Domain\ReadModel\TopProduct;

interface DashboardRepositoryInterface
{
    public function getStats(int $restaurantId): DashboardStats;

    /** @return SaleThisMonth[] */
    public function getSalesThisMonth(int $restaurantId, int $limit = 10): array;

    /** @return TopProduct[] */
    public function getTopProducts(int $restaurantId, int $limit = 5): array;

    /** @return SaleByDay[] */
    public function getSalesByDay(int $restaurantId, int $days = 30): array;
}
