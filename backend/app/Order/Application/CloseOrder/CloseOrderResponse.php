<?php

declare(strict_types=1);

namespace App\Order\Application\CloseOrder;

use App\Order\Domain\Entity\Order;

final readonly class CloseOrderResponse
{
    private function __construct(
        public string $uuid,
        public string $status,
        public int $total,
        public int $ticketNumber,
        public string $closedByUserId,
        public string $closedAt,
    ) {}

    public static function create(Order $order, int $total, int $ticketNumber): self
    {
        return new self(
            $order->getUuid()->getValue(),
            $order->getStatus()->getValue(),
            $total,
            $ticketNumber,
            $order->getClosedByUserId()->getValue(),
            $order->getClosedAt()->format('Y-m-d H:i:s'),
        );
    }
}