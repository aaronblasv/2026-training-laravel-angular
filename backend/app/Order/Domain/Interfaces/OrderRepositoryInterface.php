<?php

declare(strict_types=1);

namespace App\Order\Domain\Interfaces;

use App\Order\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(string $uuid): ?Order;
    public function findOpenByTableId(string $tableUuid): ?Order;
    public function update(Order $order): void;
    public function delete(string $uuid): void;
    public function findAllOpen(): array;
}