<?php

declare(strict_types=1);

namespace App\Order\Domain\Exception;

use App\Shared\Domain\Exception\BusinessRuleViolationException;

final class VoidQuantityExceedsOrderLineQuantityException extends BusinessRuleViolationException
{
    public function __construct(string $lineUuid, int $requestedQuantity, int $availableQuantity)
    {
        parent::__construct("Cannot void {$requestedQuantity} units from order line '{$lineUuid}' because only {$availableQuantity} are available.");
    }
}
