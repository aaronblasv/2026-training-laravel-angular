<?php

declare(strict_types=1);

namespace App\Order\Application\RemoveOrderLine;

use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;

class RemoveOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $repository,
    ) {}

    public function __invoke(string $lineUuid): void
    {
        $line = $this->repository->findById($lineUuid);
        if (!$line) {
            throw new \DomainException('Order line not found.');
        }

        $this->repository->delete($lineUuid);
    }
}