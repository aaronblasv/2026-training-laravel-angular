<?php

declare(strict_types=1);

namespace App\Sale\Infrastructure\Persistence\Repositories;

use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Entity\SaleLine;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Sale\Infrastructure\Persistence\Models\EloquentSale;
use App\Sale\Infrastructure\Persistence\Models\EloquentSaleLine;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Order\Infrastructure\Persistence\Models\EloquentOrderLine;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

class EloquentSaleRepository implements SaleRepositoryInterface
{
    public function save(Sale $sale): void
    {
        $orderId = EloquentOrder::where('uuid', $sale->getOrderId()->getValue())->firstOrFail()->id;
        $userId = EloquentUser::where('uuid', $sale->getUserId()->getValue())->firstOrFail()->id;

        EloquentSale::create([
            'uuid' => $sale->getUuid()->getValue(),
            'restaurant_id' => $sale->getRestaurantId(),
            'order_id' => $orderId,
            'user_id' => $userId,
            'ticket_number' => $sale->getTicketNumber(),
            'value_date' => $sale->getValueDate()->format('Y-m-d H:i:s'),
            'total' => $sale->getTotal(),
        ]);
    }

    public function saveLine(SaleLine $line): void
    {
        $saleId = EloquentSale::where('uuid', $line->getSaleId()->getValue())->firstOrFail()->id;
        $orderLineId = EloquentOrderLine::where('uuid', $line->getOrderLineId()->getValue())->firstOrFail()->id;
        $userId = EloquentUser::where('uuid', $line->getUserId()->getValue())->firstOrFail()->id;

        EloquentSaleLine::create([
            'uuid' => $line->getUuid()->getValue(),
            'restaurant_id' => $line->getRestaurantId(),
            'sale_id' => $saleId,
            'order_line_id' => $orderLineId,
            'user_id' => $userId,
            'quantity' => $line->getQuantity(),
            'price' => $line->getPrice(),
            'tax_percentage' => $line->getTaxPercentage(),
        ]);
    }

    public function getNextTicketNumber(int $restaurantId): int
    {
        $last = EloquentSale::where('restaurant_id', $restaurantId)
            ->max('ticket_number');

        return ($last ?? 0) + 1;
    }
}