<?php

declare(strict_types=1);

namespace App\Order\Application\UpdateOrderLineDiscount;

use App\Order\Domain\Exception\OrderLineNotFoundException;
use App\Order\Domain\Interfaces\OrderLineRepositoryInterface;

class UpdateOrderLineDiscount
{
    public function __construct(private OrderLineRepositoryInterface $orderLineRepository) {}

    public function __invoke(string $lineUuid, ?string $discountType, int $discountValue, int $restaurantId): void
    {
        $line = $this->orderLineRepository->findById($lineUuid, $restaurantId);
        if (!$line) {
            throw new OrderLineNotFoundException($lineUuid);
        }

        $line->applyDiscount($discountType, $discountValue);
        $this->orderLineRepository->update($line);
    }
}