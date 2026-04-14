<?php

declare(strict_types=1);

namespace App\Order\Application\GetAllOpenOrders;

use App\Order\Domain\Entity\Order;

final readonly class GetAllOpenOrdersResponse
{
    private function __construct(
        public string $uuid,
        public string $status,
        public string $tableId,
        public string $openedByUserId,
        public int $diners,
        public string $openedAt,
    ) {}

    public static function create(Order $order): self
    {
        return new self(
            $order->uuid()->getValue(),
            $order->status()->getValue(),
            $order->tableId()->getValue(),
            $order->openedByUserId()->getValue(),
            $order->diners()->getValue(),
            $order->openedAt()->format('Y-m-d H:i:s'),
        );
    }
}
