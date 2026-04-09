<?php

declare(strict_types=1);

namespace App\Order\Application\CancelOrder;

use App\Order\Domain\Interfaces\OrderRepositoryInterface;

class CancelOrder
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function __invoke(string $orderUuid): void
    {
        $order = $this->repository->findById($orderUuid);
        if (!$order) {
            throw new \DomainException('Order not found.');
        }
        if (!$order->getStatus()->isOpen()) {
            throw new \DomainException('Cannot cancel an order that is not open.');
        }

        $this->repository->delete($orderUuid);
    }
}