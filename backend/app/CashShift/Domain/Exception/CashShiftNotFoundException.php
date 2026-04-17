<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Exception;

use App\Shared\Domain\Exception\NotFoundException;

final class CashShiftNotFoundException extends NotFoundException
{
    public function __construct(string $uuid = 'current')
    {
        parent::__construct("Cash shift '{$uuid}' not found.");
    }
}