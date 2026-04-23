<?php

declare(strict_types=1);

namespace App\CashShift\Domain\Exception;

final class CashShiftPersistenceRelationNotFoundException extends \RuntimeException
{
    public static function missingOpenedByUser(string $cashShiftUuid, int $userId): self
    {
        return new self("Cash shift '{$cashShiftUuid}' references missing opened_by_user_id '{$userId}'.");
    }

    public static function missingClosedByUser(string $cashShiftUuid, int $userId): self
    {
        return new self("Cash shift '{$cashShiftUuid}' references missing closed_by_user_id '{$userId}'.");
    }
}