<?php

declare(strict_types=1);

namespace App\Order\Application\TransferOrderTable;

use App\Order\Domain\Exception\CannotTransferClosedOrderException;
use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Exception\TargetTableAlreadyHasOpenOrderException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Shared\Application\Context\AuditContext;
use App\Shared\Domain\Event\ActionLogged;
use App\Shared\Domain\Interfaces\DomainEventBusInterface;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Domain\Exception\TableNotFoundException;
use App\Table\Domain\Interfaces\TableRepositoryInterface;

class TransferOrderTable
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private TableRepositoryInterface $tableRepository,
        private TransactionManagerInterface $transactionManager,
        private DomainEventBusInterface $domainEventBus,
    ) {}

    public function __invoke(AuditContext $auditContext, string $orderUuid, string $targetTableUuid): void
    {
        $this->transactionManager->run(function () use ($auditContext, $orderUuid, $targetTableUuid) {
            $order = $this->orderRepository->findById($orderUuid, $auditContext->restaurantId);
            if (!$order) {
                throw new OrderNotFoundException($orderUuid);
            }

            if (!$order->status()->isOpen()) {
                throw new CannotTransferClosedOrderException($orderUuid);
            }

            $targetTable = $this->tableRepository->findById($targetTableUuid, $auditContext->restaurantId);
            if (!$targetTable) {
                throw new TableNotFoundException($targetTableUuid);
            }

            if ($order->tableId()->getValue() === $targetTableUuid) {
                return;
            }

            $existingOrder = $this->orderRepository->findOpenByTableId($targetTableUuid, $auditContext->restaurantId);
            if ($existingOrder) {
                throw new TargetTableAlreadyHasOpenOrderException($targetTableUuid);
            }

            $order->moveToTable(Uuid::create($targetTableUuid));
            $this->orderRepository->update($order);

            $order->recordDomainEvent(ActionLogged::create(
                $auditContext->restaurantId,
                $auditContext->userId,
                'order.transferred',
                'order',
                $orderUuid,
                ['target_table_id' => $targetTableUuid],
                $auditContext->ipAddress,
            ));

            $this->domainEventBus->dispatch(...$order->pullDomainEvents());
        });
    }
}