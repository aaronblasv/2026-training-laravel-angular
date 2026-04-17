<?php

declare(strict_types=1);

namespace App\Refund\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class RefundExceedsAvailableQuantityException extends BusinessRuleViolationException
{
    public function __construct(string $saleLineUuid)
    {
        parent::__construct("Refund quantity exceeds available quantity for sale line '{$saleLineUuid}'.");
    }
}