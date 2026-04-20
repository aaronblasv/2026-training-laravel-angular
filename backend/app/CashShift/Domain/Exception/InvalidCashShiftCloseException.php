<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class InvalidCashShiftCloseException extends BusinessRuleViolationException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}