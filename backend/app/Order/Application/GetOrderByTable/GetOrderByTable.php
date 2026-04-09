<?php

declare(strict_types=1);

namespace App\Order\Application\GetOrderByTable;

use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;

class GetOrderByTable
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderLineRepositoryInterface $lineRepository,
    ) {}

    public function __invoke(string $tableUuid): ?GetOrderByTableResponse
    {
        $order = $this->orderRepository->findOpenByTableId($tableUuid);
        if (!$order) {
            return null;
        }

        $lines = $this->lineRepository->findAllByOrderId($order->getUuid()->getValue());

        return GetOrderByTableResponse::create($order, $lines);
    }
}