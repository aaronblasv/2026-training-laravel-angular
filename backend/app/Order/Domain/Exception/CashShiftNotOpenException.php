<?php

declare(strict_types=1);

namespace App\Order\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class CashShiftNotOpenException extends BusinessRuleViolationException
{
    public function __construct()
    {
        parent::__construct('No se puede abrir una mesa si la caja no está abierta por un supervisor o admin.');
    }
}