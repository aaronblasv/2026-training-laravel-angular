<?php

declare(strict_types=1);

namespace App\Dashboard\Domain\Interfaces;

interface DashboardRepositoryInterface
{
    public function getStats(int $restaurantId): array;
    public function getSalesThisMonth(int $restaurantId, int $limit = 10): array;
    public function getTopProducts(int $restaurantId, int $limit = 5): array;
    public function getSalesByDay(int $restaurantId, int $days = 30): array;
}
