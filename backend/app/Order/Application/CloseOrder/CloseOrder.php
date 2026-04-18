<?php

declare(strict_types=1);

namespace App\Order\Application\CloseOrder;

use App\Order\Domain\Event\OrderClosed;
use App\Order\Domain\Exception\CannotCloseOrderWithNoLinesException;
use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;

class CloseOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderLineRepositoryInterface $lineRepository,
        private SaleRepositoryInterface $saleRepository,
    ) {}

    public function __invoke(string $orderUuid, string $closedByUserUuid, int $restaurantId): CloseOrderResponse
    {
        $order = $this->orderRepository->findById($orderUuid, $restaurantId);
        if (!$order) {
            throw new OrderNotFoundException($orderUuid);
        }

        $lines = $this->lineRepository->findAllByOrderId($orderUuid, $restaurantId);
        if (empty($lines)) {
            throw new CannotCloseOrderWithNoLinesException($orderUuid);
        }

        $subtotal = $order->calculateSubtotal($lines);
        $taxAmount = $order->calculateTaxAmount($lines);
        $lineDiscountTotal = $order->calculateLineDiscountTotal($lines);
        $orderDiscountTotal = $order->calculateOrderDiscountAmount($lines);
        $total = $subtotal + $taxAmount;

        $order->close(Uuid::create($closedByUserUuid));

        $this->orderRepository->update($order);

        $ticketNumber = $this->saleRepository->getNextTicketNumber($order->restaurantId());

        event(new OrderClosed(
            orderUuid: $order->uuid(),
            restaurantId: $order->restaurantId(),
            closedByUserUuid: Uuid::create($closedByUserUuid),
            subtotal: $subtotal,
            taxAmount: $taxAmount,
            lineDiscountTotal: $lineDiscountTotal,
            orderDiscountTotal: $orderDiscountTotal,
            total: $total,
            lines: $lines,
        ));

        return CloseOrderResponse::create($order, $total, $ticketNumber);
    }
}
