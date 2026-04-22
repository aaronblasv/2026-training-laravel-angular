<?php

declare(strict_types=1);

namespace App\Order\Application\UpdateOrderDiscount;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Shared\Application\Context\AuditContext;
use App\Shared\Domain\Event\ActionLogged;
use App\Shared\Domain\Interfaces\DomainEventBusInterface;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;

class UpdateOrderDiscount
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderLineRepositoryInterface $orderLineRepository,
        private TransactionManagerInterface $transactionManager,
        private DomainEventBusInterface $domainEventBus,
    ) {}

    public function __invoke(AuditContext $auditContext, string $orderUuid, ?string $discountType, int $discountValue): void
    {
        $this->transactionManager->run(function () use ($auditContext, $orderUuid, $discountType, $discountValue) {
            $order = $this->orderRepository->findById($orderUuid, $auditContext->restaurantId);
            if (! $order) {
                throw new OrderNotFoundException($orderUuid);
            }

            $lines = $this->orderLineRepository->findAllByOrderId($orderUuid, $auditContext->restaurantId);
            $baseAmount = $order->calculateDiscountBase($lines);

            $order->applyDiscount($discountType, $discountValue, $baseAmount);
            $this->orderRepository->update($order);

            $order->recordDomainEvent(ActionLogged::create(
                $auditContext->restaurantId,
                $auditContext->userId,
                'order.discount.updated',
                'order',
                $orderUuid,
                [
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                ],
                $auditContext->ipAddress,
            ));

            $this->domainEventBus->dispatch(...$order->pullDomainEvents());
        });
    }
}
