<?php

declare(strict_types=1);

namespace App\Sale\Domain\Interfaces;

use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Entity\SaleLine;

interface SaleRepositoryInterface
{
    public function save(Sale $sale): void;
    public function saveLine(SaleLine $line): void;
    public function getNextTicketNumber(int $restaurantId): int;
    public function findAll(int $restaurantId): array;
}