<?php

declare(strict_types=1);

namespace App\Order\Application\TransferOrderTable;

use App\Order\Domain\Exception\CannotTransferClosedOrderException;
use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Exception\TargetTableAlreadyHasOpenOrderException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Domain\Exception\TableNotFoundException;
use App\Table\Domain\Interfaces\TableRepositoryInterface;

class TransferOrderTable
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(string $orderUuid, string $targetTableUuid, int $restaurantId): void
    {
        $order = $this->orderRepository->findById($orderUuid, $restaurantId);
        if (!$order) {
            throw new OrderNotFoundException($orderUuid);
        }

        if (!$order->status()->isOpen()) {
            throw new CannotTransferClosedOrderException($orderUuid);
        }

        $targetTable = $this->tableRepository->findById($targetTableUuid, $restaurantId);
        if (!$targetTable) {
            throw new TableNotFoundException($targetTableUuid);
        }

        if ($order->tableId()->getValue() === $targetTableUuid) {
            return;
        }

        $existingOrder = $this->orderRepository->findOpenByTableId($targetTableUuid, $restaurantId);
        if ($existingOrder) {
            throw new TargetTableAlreadyHasOpenOrderException($targetTableUuid);
        }

        $order->moveToTable(Uuid::create($targetTableUuid));
        $this->orderRepository->update($order);
    }
}