<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use App\Order\Domain\ValueObject\Quantity;

class OrderLine
{
    private function __construct(
        private Uuid $uuid,
        private int $restaurantId,
        private Uuid $orderId,
        private Uuid $productId,
        private Uuid $userId,
        private Quantity $quantity,
        private int $price,
        private int $taxPercentage,
    ) {}

    public static function dddRestore(
        Uuid $uuid,
        int $restaurantId,
        Uuid $orderId,
        Uuid $productId,
        Uuid $userId,
        Quantity $quantity,
        int $price,
        int $taxPercentage,
    ): self {
        return new self($uuid, $restaurantId, $orderId, $productId, $userId, $quantity, $price, $taxPercentage);
    }

    public static function dddCreate(
        Uuid $uuid,
        int $restaurantId,
        Uuid $orderId,
        Uuid $productId,
        Uuid $userId,
        Quantity $quantity,
        int $price,
        int $taxPercentage,
    ): self {
        return new self($uuid, $restaurantId, $orderId, $productId, $userId, $quantity, $price, $taxPercentage);
    }

    public function updateQuantity(Quantity $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getUuid(): Uuid { return $this->uuid; }
    public function getRestaurantId(): int { return $this->restaurantId; }
    public function getOrderId(): Uuid { return $this->orderId; }
    public function getProductId(): Uuid { return $this->productId; }
    public function getUserId(): Uuid { return $this->userId; }
    public function getQuantity(): Quantity { return $this->quantity; }
    public function getPrice(): int { return $this->price; }
    public function getTaxPercentage(): int { return $this->taxPercentage; }
}