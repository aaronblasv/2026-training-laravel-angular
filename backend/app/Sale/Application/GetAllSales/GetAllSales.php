<?php

declare(strict_types=1);

namespace App\Sale\Application\GetAllSales;

use App\Sale\Domain\Interfaces\SaleRepositoryInterface;

class GetAllSales
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    public function __invoke(int $restaurantId): array
    {
        $sales = $this->saleRepository->findAll($restaurantId);

        return array_map(
            fn($sale) => GetAllSalesResponse::create($sale),
            $sales
        );
    }
}
