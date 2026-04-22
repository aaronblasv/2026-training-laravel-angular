<?php

declare(strict_types=1);

namespace App\Order\Application\GetAllOpenOrders;

use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;

class GetAllOpenOrders
{
    public function __construct(
        private OrderRepositoryInterface $repository,
        private OrderLineRepositoryInterface $lineRepository,
    ) {}

    public function __invoke(int $restaurantId): array
    {
        $orders = $this->repository->findAllOpen($restaurantId);
        $linesByOrderUuid = $this->lineRepository->findAllByOrderIds(
            array_map(static fn ($order) => $order->uuid()->getValue(), $orders),
            $restaurantId,
        );

        return array_map(function ($order) use ($linesByOrderUuid) {
            $lines = $linesByOrderUuid[$order->uuid()->getValue()] ?? [];

            return GetAllOpenOrdersResponse::create($order, $lines);
        }, $orders);
    }
}
