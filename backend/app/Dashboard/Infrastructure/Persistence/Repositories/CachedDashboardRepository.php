<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\Persistence\Repositories;

use App\Dashboard\Domain\Interfaces\DashboardRepositoryInterface;
use App\Dashboard\Domain\ReadModel\DashboardStats;
use App\Shared\Domain\CacheRepositoryInterface;

class CachedDashboardRepository implements DashboardRepositoryInterface
{
    private const TTL_SECONDS = 300; // 5 minutes

    public function __construct(
        private DashboardRepositoryInterface $inner,
        private CacheRepositoryInterface $cacheRepository,
    ) {}

    public function getStats(int $restaurantId): DashboardStats
    {
        return $this->cacheRepository->remember(
            "dashboard:{$restaurantId}:stats",
            self::TTL_SECONDS,
            fn() => $this->inner->getStats($restaurantId),
        );
    }

    public function getSalesThisMonth(int $restaurantId, int $limit = 10): array
    {
        return $this->cacheRepository->remember(
            "dashboard:{$restaurantId}:sales_month:{$limit}",
            self::TTL_SECONDS,
            fn() => $this->inner->getSalesThisMonth($restaurantId, $limit),
        );
    }

    public function getTopProducts(int $restaurantId, int $limit = 5): array
    {
        return $this->cacheRepository->remember(
            "dashboard:{$restaurantId}:top_products:{$limit}",
            self::TTL_SECONDS,
            fn() => $this->inner->getTopProducts($restaurantId, $limit),
        );
    }

    public function getSalesByDay(int $restaurantId, int $days = 30): array
    {
        return $this->cacheRepository->remember(
            "dashboard:{$restaurantId}:sales_by_day:{$days}",
            self::TTL_SECONDS,
            fn() => $this->inner->getSalesByDay($restaurantId, $days),
        );
    }
}
