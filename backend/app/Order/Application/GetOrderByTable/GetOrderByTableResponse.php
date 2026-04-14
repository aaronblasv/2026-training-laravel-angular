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
            $order->uuid()->getValue(),
            $order->status()->getValue(),
            $order->tableId()->getValue(),
            $order->openedByUserId()->getValue(),
            $order->diners()->getValue(),
            $order->openedAt()->format('Y-m-d H:i:s'),
            array_map(fn(OrderLine $line) => [
                'uuid'          => $line->uuid()->getValue(),
                'productId'     => $line->productId()->getValue(),
                'userId'        => $line->userId()->getValue(),
                'quantity'      => $line->quantity()->getValue(),
                'price'         => $line->price(),
                'taxPercentage' => $line->taxPercentage(),
            ], $lines),
        );
    }
}