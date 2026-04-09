<?php

declare(strict_types=1);

namespace App\Sale\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;

class SaleLine
{
    private function __construct(
        private Uuid $uuid,
        private int $restaurantId,
        private Uuid $saleId,
        private Uuid $orderLineId,
        private Uuid $userId,
        private int $quantity,
        private int $price,
        private int $taxPercentage,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        int $restaurantId,
        Uuid $saleId,
        Uuid $orderLineId,
        Uuid $userId,
        int $quantity,
        int $price,
        int $taxPercentage,
    ): self {
        return new self($uuid, $restaurantId, $saleId, $orderLineId, $userId, $quantity, $price, $taxPercentage);
    }

    public function getUuid(): Uuid { return $this->uuid; }
    public function getRestaurantId(): int { return $this->restaurantId; }
    public function getSaleId(): Uuid { return $this->saleId; }
    public function getOrderLineId(): Uuid { return $this->orderLineId; }
    public function getUserId(): Uuid { return $this->userId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getPrice(): int { return $this->price; }
    public function getTaxPercentage(): int { return $this->taxPercentage; }
}