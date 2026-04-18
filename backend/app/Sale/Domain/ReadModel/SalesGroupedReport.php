<?php

declare(strict_types=1);

namespace App\Sale\Domain\ReadModel;

final readonly class SalesGroupedReport
{
    /**
     * @param SalesReportByDay[] $byDay
     * @param SalesReportByZone[] $byZone
     * @param SalesReportByProduct[] $byProduct
     * @param SalesReportByUser[] $byUser
     */
    public function __construct(
        public array $byDay,
        public array $byZone,
        public array $byProduct,
        public array $byUser,
    ) {}

    /**
     * @return array{by_day: array, by_zone: array, by_product: array, by_user: array}
     */
    public function toArray(): array
    {
        return [
            'by_day'     => array_map(fn(SalesReportByDay $r) => ['day' => $r->day, 'count' => $r->count, 'total' => $r->total], $this->byDay),
            'by_zone'    => array_map(fn(SalesReportByZone $r) => ['zone_name' => $r->zoneName, 'count' => $r->count, 'total' => $r->total], $this->byZone),
            'by_product' => array_map(fn(SalesReportByProduct $r) => ['product_name' => $r->productName, 'total_quantity' => $r->totalQuantity, 'total' => $r->total], $this->byProduct),
            'by_user'    => array_map(fn(SalesReportByUser $r) => ['user_name' => $r->userName, 'count' => $r->count, 'total' => $r->total], $this->byUser),
        ];
    }
}
