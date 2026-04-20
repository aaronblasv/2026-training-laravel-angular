<?php

declare(strict_types=1);

namespace App\Shared\Application\Context;

final readonly class AuditContext
{
    public function __construct(
        public int $restaurantId,
        public string $userId,
        public ?string $ipAddress,
    ) {
    }
}