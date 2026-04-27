<?php

declare(strict_types=1);

namespace App\Order\Application\VoidSentOrderLine;

use App\Order\Domain\Exception\CannotVoidOrderLineWithPaymentsException;
use App\Order\Domain\Exception\CannotVoidPendingOrderLineException;
use App\Order\Domain\Exception\OrderLineNotFoundException;
use App\Order\Domain\Exception\OrderLineNotFoundInOrderContextException;
use App\Order\Domain\Exception\VoidQuantityExceedsOrderLineQuantityException;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\ValueObject\Quantity;
use App\Payment\Domain\Interfaces\PaymentRepositoryInterface;
use App\Shared\Application\Context\AuditContext;
use App\Shared\Domain\Event\ActionLogged;
use App\Shared\Domain\Interfaces\DomainEventBusInterface;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;

class VoidSentOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $lineRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private TransactionManagerInterface $transactionManager,
        private DomainEventBusInterface $domainEventBus,
    ) {}

    public function __invoke(AuditContext $auditContext, string $orderUuid, string $lineUuid, int $quantityToVoid = 1): void
    {
        $this->transactionManager->run(function () use ($auditContext, $orderUuid, $lineUuid, $quantityToVoid) {
            $line = $this->lineRepository->findById($lineUuid, $auditContext->restaurantId);

            if (! $line) {
                throw new OrderLineNotFoundException($lineUuid);
            }

            if ($line->orderId()->getValue() !== $orderUuid) {
                throw new OrderLineNotFoundInOrderContextException($lineUuid, $orderUuid);
            }

            if (! $line->isSentToKitchen()) {
                throw new CannotVoidPendingOrderLineException($lineUuid);
            }

            if ($this->paymentRepository->getTotalPaidByOrder($orderUuid) > 0) {
                throw new CannotVoidOrderLineWithPaymentsException($orderUuid);
            }

            $currentQuantity = $line->quantity()->getValue();

            if ($quantityToVoid > $currentQuantity) {
                throw new VoidQuantityExceedsOrderLineQuantityException($lineUuid, $quantityToVoid, $currentQuantity);
            }

            $remainingQuantity = $currentQuantity - $quantityToVoid;

            if ($remainingQuantity > 0) {
                $line->updateQuantity(Quantity::create($remainingQuantity));
                $this->lineRepository->update($line);
            } else {
                $this->lineRepository->delete($lineUuid, $auditContext->restaurantId);
            }

            $line->recordDomainEvent(ActionLogged::create(
                $auditContext->restaurantId,
                $auditContext->userId,
                'order.line.voided_after_kitchen',
                'order',
                $orderUuid,
                [
                    'line_uuid' => $lineUuid,
                    'product_id' => $line->productId()->getValue(),
                    'voided_quantity' => $quantityToVoid,
                    'remaining_quantity' => max(0, $remainingQuantity),
                ],
                $auditContext->ipAddress,
            ));

            $this->domainEventBus->dispatch(...$line->pullDomainEvents());
        });
    }
}
