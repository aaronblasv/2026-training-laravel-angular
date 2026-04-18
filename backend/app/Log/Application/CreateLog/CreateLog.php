<?php

declare(strict_types=1);

namespace App\Log\Application\CreateLog;

use App\Log\Domain\Entity\Log;
use App\Log\Domain\Interfaces\LogRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;

class CreateLog
{
    public function __construct(private LogRepositoryInterface $repository) {}

    public function __invoke(
        ?int $restaurantId,
        ?string $userId,
        string $action,
        ?string $entityType = null,
        ?string $entityUuid = null,
        ?array $data = null,
        ?string $ipAddress = null,
    ): void {
        $log = Log::dddCreate(
            Uuid::generate(),
            $restaurantId,
            $userId,
            $action,
            $entityType,
            $entityUuid,
            $data,
            $ipAddress,
        );

        $this->repository->save($log);
    }
}
