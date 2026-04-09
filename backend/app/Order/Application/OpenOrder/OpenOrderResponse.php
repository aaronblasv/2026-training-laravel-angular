<?php

declare(strict_types=1);

namespace App\Order\Application\OpenOrder;

use App\Order\Domain\Entity\Order;

final readonly class OpenOrderResponse
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
            $order->getUuid()->getValue(),
            $order->getStatus()->getValue(),
            $order->getTableId()->getValue(),
            $order->getOpenedByUserId()->getValue(),
            $order->getDiners()->getValue(),
            $order->getOpenedAt()->format('Y-m-d H:i:s'),
        );
    }
}