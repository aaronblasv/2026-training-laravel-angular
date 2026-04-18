<?php

declare(strict_types=1);

namespace App\Sale\Application\GetSalesReport;

use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Sale\Domain\ReadModel\SalesGroupedReport;
use Illuminate\Support\Facades\Cache;

class GetSalesReport
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    public function __invoke(int $restaurantId, ?string $from, ?string $to): SalesGroupedReport
    {
        $cacheKey = "sales_report:{$restaurantId}:{$from}:{$to}";

        return Cache::remember($cacheKey, 300, fn() =>
            $this->saleRepository->getGroupedReport($restaurantId, $from, $to)
        );
    }
}
