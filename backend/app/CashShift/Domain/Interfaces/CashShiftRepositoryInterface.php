<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Interfaces;

use App\CashShift\Domain\Entity\CashShift;

interface CashShiftRepositoryInterface
{
    public function save(CashShift $cashShift): void;
    public function update(CashShift $cashShift): void;
    public function findOpenByRestaurant(int $restaurantId): ?CashShift;
    public function findByUuid(int $restaurantId, string $uuid): ?CashShift;

    /** @return array{cash_total:int,card_total:int,bizum_total:int,refund_total:int} */
    public function getWindowSummary(int $restaurantId, \DateTimeImmutable $from, ?\DateTimeImmutable $to): array;
}