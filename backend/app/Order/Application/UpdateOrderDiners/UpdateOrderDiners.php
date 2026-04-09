<?php

declare(strict_types=1);

namespace App\Order\Application\UpdateOrderDiners;

use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\Diners;

class UpdateOrderDiners
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function __invoke(string $orderUuid, int $diners): void
    {
        $order = $this->repository->findById($orderUuid);
        if (!$order) {
            throw new \DomainException('Order not found.');
        }
        if (!$order->getStatus()->isOpen()) {
            throw new \DomainException('Cannot update diners on a closed order.');
        }

        $order->updateDiners(Diners::create($diners));
        $this->repository->update($order);
    }
}