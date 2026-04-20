<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Interfaces;

use App\CashShift\Domain\ReadModel\CashShiftSummary;

interface CashShiftSalesReadModelInterface
{
    public function getWindowSummary(int $restaurantId, \DateTimeImmutable $from, ?\DateTimeImmutable $to): CashShiftSummary;
}