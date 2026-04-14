<?php

declare(strict_types=1);

namespace App\Dashboard\Infrastructure\Persistence\Repositories;

use App\Dashboard\Domain\Interfaces\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    public function getStats(int $restaurantId): array
    {
        $now = now();

        return [
            'products'         => DB::table('products')->where('restaurant_id', $restaurantId)->whereNull('deleted_at')->count(),
            'families'         => DB::table('families')->where('restaurant_id', $restaurantId)->whereNull('deleted_at')->count(),
            'taxes'            => DB::table('taxes')->where('restaurant_id', $restaurantId)->whereNull('deleted_at')->count(),
            'users'            => DB::table('users')->where('restaurant_id', $restaurantId)->whereNull('deleted_at')->count(),
            'sales_this_month' => DB::table('sales')
                ->where('restaurant_id', $restaurantId)
                ->whereNull('deleted_at')
                ->whereYear('value_date', $now->year)
                ->whereMonth('value_date', $now->month)
                ->count(),
            'revenue_this_month' => (int) DB::table('sales')
                ->where('restaurant_id', $restaurantId)
                ->whereNull('deleted_at')
                ->whereYear('value_date', $now->year)
                ->whereMonth('value_date', $now->month)
                ->sum('total'),
        ];
    }

    public function getSalesThisMonth(int $restaurantId, int $limit = 10): array
    {
        $now = now();

        return DB::table('sales as s')
            ->join('orders as o', 's.order_id', '=', 'o.id')
            ->join('tables as t', 'o.table_id', '=', 't.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->where('s.restaurant_id', $restaurantId)
            ->whereNull('s.deleted_at')
            ->whereYear('s.value_date', $now->year)
            ->whereMonth('s.value_date', $now->month)
            ->orderBy('s.value_date', 'desc')
            ->limit($limit)
            ->select(
                's.uuid',
                's.ticket_number',
                's.total',
                's.value_date',
                't.name as table_name',
                'u.name as user_name',
            )
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    public function getTopProducts(int $restaurantId, int $limit = 5): array
    {
        return DB::table('sales_lines as sl')
            ->join('order_lines as ol', 'sl.order_line_id', '=', 'ol.id')
            ->join('products as p', 'ol.product_id', '=', 'p.id')
            ->where('sl.restaurant_id', $restaurantId)
            ->whereNull('sl.deleted_at')
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->select(
                'p.name',
                DB::raw('SUM(sl.quantity) as total_quantity'),
                DB::raw('SUM(sl.quantity * sl.price) as total_revenue'),
            )
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    public function getSalesByDay(int $restaurantId, int $days = 30): array
    {
        return DB::table('sales')
            ->where('restaurant_id', $restaurantId)
            ->whereNull('deleted_at')
            ->where('value_date', '>=', now()->subDays($days)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->select(
                DB::raw('DATE(value_date) as day'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total'),
            )
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }
}
