<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class CashShiftAlreadyClosedException extends BusinessRuleViolationException
{
    public function __construct()
    {
        parent::__construct('Cash shift is already closed.');
    }
}