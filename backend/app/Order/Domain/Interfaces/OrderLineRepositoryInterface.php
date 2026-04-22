<?php

declare(strict_types=1);

namespace App\Order\Domain\Interfaces;

use App\Order\Domain\Entity\OrderLine;

interface OrderLineRepositoryInterface
{
    public function save(OrderLine $line): void;

    public function findById(string $uuid, int $restaurantId): ?OrderLine;

    public function findAllByOrderId(string $orderUuid, int $restaurantId): array;

    public function findAllByOrderIds(array $orderUuids, int $restaurantId): array;

    public function update(OrderLine $line): void;

    public function delete(string $uuid, int $restaurantId): void;
}
