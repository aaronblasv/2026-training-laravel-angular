<?php

declare(strict_types=1);

namespace App\Order\Application\AddOrderLine;

use App\Order\Domain\Entity\OrderLine;

final readonly class AddOrderLineResponse
{
    private function __construct(
        public string $uuid,
        public string $productId,
        public string $userId,
        public int $quantity,
        public int $price,
        public int $taxPercentage,
    ) {}

    public static function create(OrderLine $line): self
    {
        return new self(
            $line->getUuid()->getValue(),
            $line->getProductId()->getValue(),
            $line->getUserId()->getValue(),
            $line->getQuantity()->getValue(),
            $line->getPrice(),
            $line->getTaxPercentage(),
        );
    }
}