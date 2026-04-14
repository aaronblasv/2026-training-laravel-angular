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

    public static function fromPersistence(
        string $uuid,
        int $restaurantId,
        string $orderId,
        string $productId,
        string $userId,
        int $quantity,
        int $price,
        int $taxPercentage,
    ): self {
        return new self(
            Uuid::create($uuid),
            $restaurantId,
            Uuid::create($orderId),
            Uuid::create($productId),
            Uuid::create($userId),
            Quantity::create($quantity),
            $price,
            $taxPercentage,
        );
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

    public function uuid(): Uuid { return $this->uuid; }
    public function restaurantId(): int { return $this->restaurantId; }
    public function orderId(): Uuid { return $this->orderId; }
    public function productId(): Uuid { return $this->productId; }
    public function userId(): Uuid { return $this->userId; }
    public function quantity(): Quantity { return $this->quantity; }
    public function price(): int { return $this->price; }
    public function taxPercentage(): int { return $this->taxPercentage; }
}