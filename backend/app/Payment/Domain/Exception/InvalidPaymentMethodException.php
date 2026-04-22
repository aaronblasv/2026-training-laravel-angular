<?php

declare(strict_types=1);

namespace App\Payment\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class InvalidPaymentMethodException extends BusinessRuleViolationException
{
    public function __construct(string $value)
    {
        parent::__construct("Invalid payment method '{$value}'. Allowed: cash, card, bizum.");
    }
}