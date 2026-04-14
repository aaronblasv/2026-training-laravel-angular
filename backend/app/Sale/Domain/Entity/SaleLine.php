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

    public static function fromPersistence(
        string $uuid,
        int $restaurantId,
        string $saleId,
        string $orderLineId,
        string $userId,
        int $quantity,
        int $price,
        int $taxPercentage,
    ): self {
        return new self(
            Uuid::create($uuid),
            $restaurantId,
            Uuid::create($saleId),
            Uuid::create($orderLineId),
            Uuid::create($userId),
            $quantity,
            $price,
            $taxPercentage,
        );
    }

    public function uuid(): Uuid { return $this->uuid; }
    public function restaurantId(): int { return $this->restaurantId; }
    public function saleId(): Uuid { return $this->saleId; }
    public function orderLineId(): Uuid { return $this->orderLineId; }
    public function userId(): Uuid { return $this->userId; }
    public function quantity(): int { return $this->quantity; }
    public function price(): int { return $this->price; }
    public function taxPercentage(): int { return $this->taxPercentage; }
}