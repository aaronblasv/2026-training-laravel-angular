<?php

declare(strict_types=1);

namespace App\Order\Application\CloseOrder;

use App\Order\Domain\Exception\CannotCloseOrderWithNoLinesException;
use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Entity\SaleLine;
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
        $saleUuid = Uuid::generate();

        $sale = Sale::dddCreate(
            $saleUuid,
            $order->restaurantId(),
            $order->uuid(),
            Uuid::create($closedByUserUuid),
            $ticketNumber,
            $subtotal,
            $taxAmount,
            $lineDiscountTotal,
            $orderDiscountTotal,
            $total,
        );
        $this->saleRepository->save($sale);

        foreach ($lines as $line) {
            $saleLine = SaleLine::dddCreate(
                Uuid::generate(),
                $order->restaurantId(),
                $saleUuid,
                $line->uuid(),
                $line->userId(),
                $line->quantity()->getValue(),
                $line->price(),
                $line->taxPercentage(),
                $line->subtotal(),
                $line->taxAmount(),
                $line->discountType(),
                $line->discountValue(),
                $line->discountAmount(),
                $line->total(),
            );
            $this->saleRepository->saveLine($saleLine);
        }

        return CloseOrderResponse::create($order, $total, $ticketNumber);
    }
}
