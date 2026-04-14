<?php

declare(strict_types=1);

namespace App\Dashboard\Application\GetDashboardStats;

use App\Dashboard\Domain\Interfaces\DashboardRepositoryInterface;

class GetDashboardStats
{
    public function __construct(
        private DashboardRepositoryInterface $repository,
    ) {}

    public function __invoke(int $restaurantId): GetDashboardStatsResponse
    {
        return new GetDashboardStatsResponse(
            $this->repository->getStats($restaurantId),
            $this->repository->getSalesThisMonth($restaurantId),
            $this->repository->getTopProducts($restaurantId),
            $this->repository->getSalesByDay($restaurantId),
        );
    }
}
