<?php

declare(strict_types=1);

namespace App\Order\Application\CloseOrder;

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

    public function __invoke(string $orderUuid, string $closedByUserUuid): CloseOrderResponse
    {
        $order = $this->orderRepository->findById($orderUuid);
        if (!$order) {
            throw new \DomainException('Order not found.');
        }

        $lines = $this->lineRepository->findAllByOrderId($orderUuid);
        if (empty($lines)) {
            throw new \DomainException('Cannot close an order with no lines.');
        }

        $total = 0;
        foreach ($lines as $line) {
            $lineSubtotal = $line->getPrice() * $line->getQuantity()->getValue();
            $lineTotal = $lineSubtotal + (int) round($lineSubtotal * $line->getTaxPercentage() / 100);
            $total += $lineTotal;
        }

        $order->close(Uuid::create($closedByUserUuid));
        $this->orderRepository->update($order);

        $ticketNumber = $this->saleRepository->getNextTicketNumber($order->getRestaurantId());
        $saleUuid = Uuid::generate();

        $sale = Sale::dddCreate(
            $saleUuid,
            $order->getRestaurantId(),
            $order->getUuid(),
            Uuid::create($closedByUserUuid),
            $ticketNumber,
            $total,
        );
        $this->saleRepository->save($sale);

        foreach ($lines as $line) {
            $saleLine = SaleLine::dddCreate(
                Uuid::generate(),
                $order->getRestaurantId(),
                $saleUuid,
                $line->getUuid(),
                $line->getUserId(),
                $line->getQuantity()->getValue(),
                $line->getPrice(),
                $line->getTaxPercentage(),
            );
            $this->saleRepository->saveLine($saleLine);
        }

        return CloseOrderResponse::create($order, $total, $ticketNumber);
    }
}