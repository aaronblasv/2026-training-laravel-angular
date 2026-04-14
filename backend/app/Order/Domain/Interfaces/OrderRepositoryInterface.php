<?php

declare(strict_types=1);

namespace App\Order\Domain\Interfaces;

use App\Order\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(string $uuid, int $restaurantId): ?Order;
    public function findOpenByTableId(string $tableUuid, int $restaurantId): ?Order;
    public function update(Order $order): void;
    public function delete(string $uuid, int $restaurantId): void;
    public function findAllOpen(int $restaurantId): array;
}