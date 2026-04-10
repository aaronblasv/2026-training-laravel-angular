<?php

declare(strict_types=1);

namespace App\Order\Application\GetAllOpenOrders;

use App\Order\Domain\Interfaces\OrderRepositoryInterface;

class GetAllOpenOrders
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function __invoke(): array
    {
        return $this->repository->findAllOpen();
    }
}