<?php

declare(strict_types=1);

namespace App\Log\Infrastructure\Persistence\Repositories;

use App\Log\Domain\Entity\Log;
use App\Log\Domain\Interfaces\LogRepositoryInterface;
use App\Log\Infrastructure\Persistence\Models\EloquentLog;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Eloquent\Builder;

class EloquentLogRepository implements LogRepositoryInterface
{
    public function __construct(
        private EloquentLog $model,
        private EloquentUser $userModel,
    ) {}

    public function save(Log $log): void
    {
        $this->model->newQuery()->create([
            'uuid'        => $log->uuid()->getValue(),
            'restaurant_id' => $log->restaurantId(),
            'user_id'     => $log->userId(),
            'action'      => $log->action(),
            'entity_type' => $log->entityType(),
            'entity_uuid' => $log->entityUuid(),
            'data'        => $log->data(),
            'ip_address'  => $log->ipAddress(),
        ]);
    }

    public function findAll(
        int $restaurantId,
        ?string $action = null,
        ?string $userId = null,
        int $limit = 50,
        int $offset = 0,
    ): array
    {
        return $this->applyFilters(
                $this->model->newQuery()->where('restaurant_id', $restaurantId),
                $action,
                $userId,
            )
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn(EloquentLog $log) => $this->toDomain($log))
            ->toArray();
    }

    public function findByUser(int $restaurantId, string $userId, int $limit = 50, int $offset = 0): array
    {
        return $this->findAll($restaurantId, null, $userId, $limit, $offset);
    }

    public function findByAction(int $restaurantId, string $action, int $limit = 50, int $offset = 0): array
    {
        return $this->findAll($restaurantId, $action, null, $limit, $offset);
    }

    public function findByEntity(int $restaurantId, string $entityType, string $entityUuid, int $limit = 50, int $offset = 0): array
    {
        return $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->where('entity_type', $entityType)
            ->where('entity_uuid', $entityUuid)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn(EloquentLog $log) => $this->toDomain($log))
            ->toArray();
    }

    public function count(int $restaurantId, ?string $action = null, ?string $userId = null): int
    {
        return $this->applyFilters(
            $this->model->newQuery()->where('restaurant_id', $restaurantId),
            $action,
            $userId,
        )->count();
    }

    private function applyFilters(Builder $query, ?string $action, ?string $userId): Builder
    {
        if ($action) {
            $query->where('action', $action);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query;
    }

    private function toDomain(EloquentLog $model): Log
    {
        $createdAt = $model->created_at;
        $userName = null;

        if ($model->user_id) {
            $userName = $this->userModel->newQuery()
                ->where('uuid', $model->user_id)
                ->value('name');
        }

        return Log::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $model->user_id,
            $userName,
            $model->action,
            $model->entity_type,
            $model->entity_uuid,
            $model->data,
            $model->ip_address,
            \DateTimeImmutable::createFromInterface($createdAt),
        );
    }
}
