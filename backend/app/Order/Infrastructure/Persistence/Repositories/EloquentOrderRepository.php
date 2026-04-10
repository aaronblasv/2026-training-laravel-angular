<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Persistence\Repositories;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Shared\Domain\ValueObject\Uuid;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): void
    {
        $tableId = EloquentTable::where('uuid', $order->getTableId()->getValue())->firstOrFail()->id;
        $openedByUserId = EloquentUser::where('uuid', $order->getOpenedByUserId()->getValue())->firstOrFail()->id;

        EloquentOrder::create([
            'uuid' => $order->getUuid()->getValue(),
            'restaurant_id' => $order->getRestaurantId(),
            'status' => $order->getStatus()->getValue(),
            'table_id' => $tableId,
            'opened_by_user_id' => $openedByUserId,
            'closed_by_user_id' => null,
            'diners' => $order->getDiners()->getValue(),
            'opened_at' => $order->getOpenedAt()->format('Y-m-d H:i:s'),
            'closed_at' => null,
        ]);
    }

    public function findById(string $uuid): ?Order
    {
        $model = EloquentOrder::where('uuid', $uuid)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function delete(string $uuid): void
    {
        EloquentOrder::where('uuid', $uuid)->firstOrFail()->delete();
    }

    public function findAllOpen(): array
    {
        return EloquentOrder::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'open')
            ->get()
            ->map(fn(EloquentOrder $model) => [
                'uuid' => $model->uuid,
                'tableId' => EloquentTable::find($model->table_id)->uuid,
                'openedByUserId' => EloquentUser::find($model->opened_by_user_id)->uuid,
                'diners' => $model->diners,
                'openedAt' => $model->opened_at,
            ])
            ->toArray();
    }

    public function findOpenByTableId(string $tableUuid): ?Order
    {
        $tableId = EloquentTable::where('uuid', $tableUuid)->firstOrFail()->id;

        $model = EloquentOrder::where('table_id', $tableId)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'open')
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function update(Order $order): void
    {
        $model = EloquentOrder::where('uuid', $order->getUuid()->getValue())->firstOrFail();

        $data = [
            'status' => $order->getStatus()->getValue(),
            'diners' => $order->getDiners()->getValue(),
        ];

        if ($order->getClosedByUserId()) {
            $data['closed_by_user_id'] = EloquentUser::where('uuid', $order->getClosedByUserId()->getValue())->firstOrFail()->id;
            $data['closed_at'] = $order->getClosedAt()->format('Y-m-d H:i:s');
        }

        $model->update($data);
    }

    private function toDomain(EloquentOrder $model): Order
    {
        $tableUuid = EloquentTable::find($model->table_id)->uuid;
        $openedByUuid = EloquentUser::find($model->opened_by_user_id)->uuid;
        $closedByUuid = $model->closed_by_user_id
            ? EloquentUser::find($model->closed_by_user_id)->uuid
            : null;

        return Order::dddRestore(
            Uuid::create($model->uuid),
            $model->restaurant_id,
            OrderStatus::create($model->status),
            Uuid::create($tableUuid),
            Uuid::create($openedByUuid),
            $closedByUuid ? Uuid::create($closedByUuid) : null,
            Diners::create($model->diners),
            new \DateTimeImmutable($model->opened_at),
            $model->closed_at ? new \DateTimeImmutable($model->closed_at) : null,
        );
    }
}