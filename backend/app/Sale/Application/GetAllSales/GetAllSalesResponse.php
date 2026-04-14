<?php

declare(strict_types=1);

namespace App\Sale\Application\GetAllSales;

use App\Sale\Domain\Entity\Sale;

final readonly class GetAllSalesResponse
{
    private function __construct(
        public string $uuid,
        public int $restaurantId,
        public string $orderId,
        public string $userId,
        public int $ticketNumber,
        public string $valueDate,
        public int $total,
    ) {}

    public static function create(Sale $sale): self
    {
        return new self(
            $sale->uuid()->getValue(),
            $sale->restaurantId(),
            $sale->orderId()->getValue(),
            $sale->userId()->getValue(),
            $sale->ticketNumber(),
            $sale->valueDate()->format('Y-m-d H:i:s'),
            $sale->total(),
        );
    }
}
