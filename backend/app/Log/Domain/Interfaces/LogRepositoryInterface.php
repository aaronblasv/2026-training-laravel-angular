<?php

namespace App\Log\Domain\Interfaces;

use App\Log\Domain\Entity\Log;

interface LogRepositoryInterface
{
    public function save(Log $log): void;

    public function findAll(int $limit = 50, int $offset = 0): array;

    public function findByUser(string $userId, int $limit = 50, int $offset = 0): array;

    public function findByAction(string $action, int $limit = 50, int $offset = 0): array;

    public function findByEntity(string $entityType, string $entityUuid, int $limit = 50, int $offset = 0): array;

    public function count(): int;
}
