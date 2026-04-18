<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\Persistence\Repositories;

use App\Dashboard\Domain\Interfaces\DashboardRepositoryInterface;
use App\Dashboard\Domain\ReadModel\DashboardStats;
use Illuminate\Support\Facades\Cache;

class CachedDashboardRepository implements DashboardRepositoryInterface
{
    private const TTL_SECONDS = 300; // 5 minutes

    public function __construct(
        private EloquentDashboardRepository $inner,
    ) {}

    public function getStats(int $restaurantId): DashboardStats
    {
        return Cache::remember(
            "dashboard:stats:{$restaurantId}",
            self::TTL_SECONDS,
            fn() => $this->inner->getStats($restaurantId),
        );
    }

    public function getSalesThisMonth(int $restaurantId, int $limit = 10): array
    {
        return Cache::remember(
            "dashboard:sales_month:{$restaurantId}:{$limit}",
            self::TTL_SECONDS,
            fn() => $this->inner->getSalesThisMonth($restaurantId, $limit),
        );
    }

    public function getTopProducts(int $restaurantId, int $limit = 5): array
    {
        return Cache::remember(
            "dashboard:top_products:{$restaurantId}:{$limit}",
            self::TTL_SECONDS,
            fn() => $this->inner->getTopProducts($restaurantId, $limit),
        );
    }

    public function getSalesByDay(int $restaurantId, int $days = 30): array
    {
        return Cache::remember(
            "dashboard:sales_by_day:{$restaurantId}:{$days}",
            self::TTL_SECONDS,
            fn() => $this->inner->getSalesByDay($restaurantId, $days),
        );
    }
}
