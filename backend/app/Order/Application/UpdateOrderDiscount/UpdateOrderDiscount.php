<?php

declare(strict_types=1);

namespace App\Order\Application\UpdateOrderDiscount;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;

class UpdateOrderDiscount
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderLineRepositoryInterface $orderLineRepository,
    ) {}

    public function __invoke(string $orderUuid, ?string $discountType, int $discountValue, int $restaurantId): void
    {
        $order = $this->orderRepository->findById($orderUuid, $restaurantId);
        if (!$order) {
            throw new OrderNotFoundException($orderUuid);
        }

        $lines = $this->orderLineRepository->findAllByOrderId($orderUuid, $restaurantId);
        $baseAmount = array_reduce($lines, fn ($carry, $line) => $carry + $line->subtotalAfterDiscount(), 0);

        $order->applyDiscount($discountType, $discountValue, $baseAmount);
        $this->orderRepository->update($order);
    }
}