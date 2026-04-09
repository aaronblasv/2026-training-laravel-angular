<?php

declare(strict_types=1);

namespace App\Order\Application\GetOrderByTable;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Entity\OrderLine;

final readonly class GetOrderByTableResponse
{
    private function __construct(
        public string $uuid,
        public string $status,
        public string $tableId,
        public string $openedByUserId,
        public int $diners,
        public string $openedAt,
        public array $lines,
    ) {}

    public static function create(Order $order, array $lines): self
    {
        return new self(
            $order->getUuid()->getValue(),
            $order->getStatus()->getValue(),
            $order->getTableId()->getValue(),
            $order->getOpenedByUserId()->getValue(),
            $order->getDiners()->getValue(),
            $order->getOpenedAt()->format('Y-m-d H:i:s'),
            array_map(fn(OrderLine $line) => [
                'uuid' => $line->getUuid()->getValue(),
                'productId' => $line->getProductId()->getValue(),
                'userId' => $line->getUserId()->getValue(),
                'quantity' => $line->getQuantity()->getValue(),
                'price' => $line->getPrice(),
                'taxPercentage' => $line->getTaxPercentage(),
            ], $lines),
        );
    }
}