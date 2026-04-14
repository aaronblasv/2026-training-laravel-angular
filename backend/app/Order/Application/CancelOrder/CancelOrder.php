<?php

declare(strict_types=1);

namespace App\Order\Application\CancelOrder;

use App\Order\Domain\Interfaces\OrderRepositoryInterface;

class CancelOrder
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function __invoke(string $orderUuid, int $restaurantId): void
    {
        $order = $this->repository->findById($orderUuid, $restaurantId);
        if (!$order) {
            throw new \DomainException('Order not found.');
        }
        if (!$order->status()->isOpen()) {
            throw new \DomainException('Cannot cancel an order that is not open.');
        }

        $this->repository->delete($orderUuid, $restaurantId);
    }
}
