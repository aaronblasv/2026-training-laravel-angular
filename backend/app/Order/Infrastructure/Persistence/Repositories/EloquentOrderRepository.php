<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Persistence\Repositories;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Exception\OrderPersistenceRelationNotFoundException;
use App\Order\Domain\Exception\TableAlreadyHasOpenOrderException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\Shared\Domain\Exception\ConcurrencyException;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Table\Domain\Exception\TableNotFoundException;
use App\Table\Infrastructure\Persistence\Models\EloquentTable;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EloquentOrder $model,
        private EloquentTable $tableModel,
        private EloquentUser $userModel,
    ) {}

    public function save(Order $order): void
    {
        $tableId = $this->resolveTableId($order->tableId()->getValue());
        $openedByUserId = $this->resolveUserId($order->openedByUserId()->getValue());

        try {
            $this->model->newQuery()->create([
                'uuid' => $order->uuid()->getValue(),
                'restaurant_id' => $order->restaurantId(),
                'status' => $order->status()->value,
                'table_id' => $tableId,
                'opened_by_user_id' => $openedByUserId,
                'closed_by_user_id' => null,
                'diners' => $order->diners()->getValue(),
                'discount_type' => $order->discountType(),
                'discount_value' => $order->discountValue(),
                'discount_amount' => $order->discountAmount(),
                'opened_at' => $order->openedAt()->format('Y-m-d H:i:s'),
                'closed_at' => null,
            ]);
        } catch (QueryException $exception) {
            if ($this->isOpenTableUniqueViolation($exception)) {
                throw new TableAlreadyHasOpenOrderException($order->tableId()->getValue());
            }

            throw $exception;
        }
    }

    public function findById(string $uuid, int $restaurantId): ?Order
    {
        $model = $this->model->newQuery()
            ->with(['table', 'openedByUser', 'closedByUser'])
            ->where('uuid', $uuid)
            ->where('restaurant_id', $restaurantId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findOpenByTableId(string $tableUuid, int $restaurantId): ?Order
    {
        $table = $this->tableModel->newQuery()->where('uuid', $tableUuid)->first();
        if (! $table) {
            return null;
        }

        $model = $this->model->newQuery()
            ->where('table_id', $table->id)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'open')
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function update(Order $order): void
    {
        try {
            $this->model->newQuery()->where('uuid', $order->uuid()->getValue())->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new OrderNotFoundException($order->uuid()->getValue());
        }

        $tableId = $this->resolveTableId($order->tableId()->getValue());

        $data = [
            'table_id' => $tableId,
            'status' => $order->status()->value,
            'diners' => $order->diners()->getValue(),
            'discount_type' => $order->discountType(),
            'discount_value' => $order->discountValue(),
            'discount_amount' => $order->discountAmount(),
        ];

        if ($order->closedByUserId()) {
            $data['closed_by_user_id'] = $this->resolveUserId($order->closedByUserId()->getValue());
            $data['closed_at'] = $order->closedAt()->format('Y-m-d H:i:s');
        }

        $updatedRows = $this->model->newQuery()
            ->where('uuid', $order->uuid()->getValue())
            ->when(
                $order->persistedAt() !== null,
                fn ($query) => $query->where('updated_at', $order->persistedAt()?->format('Y-m-d H:i:s')),
            )
            ->update($data);

        if ($updatedRows === 0) {
            throw ConcurrencyException::forResource('Order', $order->uuid()->getValue());
        }

        $freshUpdatedAt = $this->model->newQuery()
            ->where('uuid', $order->uuid()->getValue())
            ->value('updated_at');

        if ($freshUpdatedAt !== null) {
            $order->syncPersistedAt(DomainDateTime::create($this->toDateTimeImmutable($freshUpdatedAt)));
        }
    }

    public function delete(string $uuid, int $restaurantId): void
    {
        try {
            $this->model->newQuery()
                ->where('uuid', $uuid)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail()
                ->delete();
        } catch (ModelNotFoundException) {
            throw new OrderNotFoundException($uuid);
        }
    }

    public function findAllOpen(int $restaurantId): array
    {
        return $this->model->newQuery()
            ->with(['table', 'openedByUser', 'closedByUser'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'open')
            ->get()
            ->map(fn (EloquentOrder $model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(EloquentOrder $model): Order
    {
        $tableUuid = $this->resolveTableUuid($model);
        $openedByUuid = $this->resolveOpenedByUserUuid($model);
        $closedByUuid = $this->resolveClosedByUserUuid($model);

        return Order::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->status,
            $tableUuid,
            $openedByUuid,
            $closedByUuid,
            $model->diners,
            $model->discount_type,
            (int) $model->discount_value,
            (int) $model->discount_amount,
            $this->toDateTimeImmutable($model->opened_at),
            $this->toDateTimeImmutable($model->closed_at),
            $this->toDateTimeImmutable($model->updated_at),
        );
    }

    private function toDateTimeImmutable(mixed $value): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($value);
        }

        return new \DateTimeImmutable((string) $value);
    }

    private function isOpenTableUniqueViolation(QueryException $exception): bool
    {
        $errorCode = $exception->errorInfo[1] ?? null;
        $message = $exception->getMessage();

        if ($errorCode === 1062 && str_contains($message, 'uniq_open_table_per_restaurant')) {
            return true;
        }

        return str_contains($message, 'uniq_open_table_per_restaurant')
            || str_contains($message, 'orders.restaurant_id, orders.open_table_guard');
    }

    private function resolveTableId(string $tableUuid): int
    {
        try {
            return $this->tableModel->newQuery()->where('uuid', $tableUuid)->firstOrFail()->id;
        } catch (ModelNotFoundException) {
            throw new TableNotFoundException($tableUuid);
        }
    }

    private function resolveUserId(string $userUuid): int
    {
        try {
            return $this->userModel->newQuery()->where('uuid', $userUuid)->firstOrFail()->id;
        } catch (ModelNotFoundException) {
            throw new UserNotFoundException($userUuid);
        }
    }

    private function resolveTableUuid(EloquentOrder $model): string
    {
        if ($model->relationLoaded('table')) {
            if ($model->table === null) {
                throw OrderPersistenceRelationNotFoundException::missingTable($model->uuid, (int) $model->table_id);
            }

            return $model->table->uuid;
        }

        $table = $this->tableModel->newQuery()->find($model->table_id);
        if ($table === null) {
            throw OrderPersistenceRelationNotFoundException::missingTable($model->uuid, (int) $model->table_id);
        }

        return $table->uuid;
    }

    private function resolveOpenedByUserUuid(EloquentOrder $model): string
    {
        if ($model->relationLoaded('openedByUser')) {
            if ($model->openedByUser === null) {
                throw OrderPersistenceRelationNotFoundException::missingOpenedByUser($model->uuid, (int) $model->opened_by_user_id);
            }

            return $model->openedByUser->uuid;
        }

        $user = $this->userModel->newQuery()->find($model->opened_by_user_id);
        if ($user === null) {
            throw OrderPersistenceRelationNotFoundException::missingOpenedByUser($model->uuid, (int) $model->opened_by_user_id);
        }

        return $user->uuid;
    }

    private function resolveClosedByUserUuid(EloquentOrder $model): ?string
    {
        if ($model->closed_by_user_id === null) {
            return null;
        }

        if ($model->relationLoaded('closedByUser')) {
            if ($model->closedByUser === null) {
                throw OrderPersistenceRelationNotFoundException::missingClosedByUser($model->uuid, (int) $model->closed_by_user_id);
            }

            return $model->closedByUser->uuid;
        }

        $user = $this->userModel->newQuery()->find($model->closed_by_user_id);
        if ($user === null) {
            throw OrderPersistenceRelationNotFoundException::missingClosedByUser($model->uuid, (int) $model->closed_by_user_id);
        }

        return $user->uuid;
    }
}
