<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Persistence\Repositories;

use App\Order\Domain\Entity\OrderLine;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Order\Domain\ValueObject\Quantity;
use App\Order\Infrastructure\Persistence\Models\EloquentOrderLine;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Product\Infrastructure\Persistence\Models\EloquentProduct;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use App\Shared\Domain\ValueObject\Uuid;

class EloquentOrderLineRepository implements OrderLineRepositoryInterface
{
    public function save(OrderLine $line): void
    {
        $orderId = EloquentOrder::where('uuid', $line->getOrderId()->getValue())->firstOrFail()->id;
        $productId = EloquentProduct::where('uuid', $line->getProductId()->getValue())->firstOrFail()->id;
        $userId = EloquentUser::where('uuid', $line->getUserId()->getValue())->firstOrFail()->id;

        EloquentOrderLine::create([
            'uuid' => $line->getUuid()->getValue(),
            'restaurant_id' => $line->getRestaurantId(),
            'order_id' => $orderId,
            'product_id' => $productId,
            'user_id' => $userId,
            'quantity' => $line->getQuantity()->getValue(),
            'price' => $line->getPrice(),
            'tax_percentage' => $line->getTaxPercentage(),
        ]);
    }

    public function findById(string $uuid): ?OrderLine
    {
        $model = EloquentOrderLine::where('uuid', $uuid)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAllByOrderId(string $orderUuid): array
    {
        $orderId = EloquentOrder::where('uuid', $orderUuid)->firstOrFail()->id;

        return EloquentOrderLine::where('order_id', $orderId)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->get()
            ->map(fn(EloquentOrderLine $model) => $this->toDomain($model))
            ->toArray();
    }

    public function update(OrderLine $line): void
    {
        $model = EloquentOrderLine::where('uuid', $line->getUuid()->getValue())->firstOrFail();
        $model->update([
            'quantity' => $line->getQuantity()->getValue(),
        ]);
    }

    public function delete(string $uuid): void
    {
        EloquentOrderLine::where('uuid', $uuid)->firstOrFail()->delete();
    }

    private function toDomain(EloquentOrderLine $model): OrderLine
    {
        $orderUuid = EloquentOrder::find($model->order_id)->uuid;
        $productUuid = EloquentProduct::find($model->product_id)->uuid;
        $userUuid = EloquentUser::find($model->user_id)->uuid;

        return OrderLine::dddRestore(
            Uuid::create($model->uuid),
            $model->restaurant_id,
            Uuid::create($orderUuid),
            Uuid::create($productUuid),
            Uuid::create($userUuid),
            Quantity::create($model->quantity),
            $model->price,
            $model->tax_percentage,
        );
    }
}