<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class CashShiftAlreadyOpenException extends BusinessRuleViolationException
{
    public function __construct()
    {
        parent::__construct('There is already an open cash shift.');
    }
}