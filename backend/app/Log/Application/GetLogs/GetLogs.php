<?php

declare(strict_types=1);

namespace App\Log\Application\GetLogs;

use App\Log\Domain\Interfaces\LogRepositoryInterface;

class GetLogs
{
    public function __construct(private LogRepositoryInterface $repository) {}

    public function __invoke(
        int $restaurantId,
        ?string $action = null,
        ?string $userId = null,
        int $limit = 50,
        int $offset = 0,
    ): GetLogsResponse {
        $logs = $this->repository->findAll($restaurantId, $action, $userId, $limit, $offset);

        $total = $this->repository->count($restaurantId, $action, $userId);

        return GetLogsResponse::create($logs, $total);
    }
}
