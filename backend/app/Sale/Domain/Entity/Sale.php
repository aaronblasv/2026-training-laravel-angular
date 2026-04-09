<?php

declare(strict_types=1);

namespace App\Sale\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;

class Sale
{
    private function __construct(
        private Uuid $uuid,
        private int $restaurantId,
        private Uuid $orderId,
        private Uuid $userId,
        private int $ticketNumber,
        private \DateTimeImmutable $valueDate,
        private int $total,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        int $restaurantId,
        Uuid $orderId,
        Uuid $userId,
        int $ticketNumber,
        int $total,
    ): self {
        return new self($uuid, $restaurantId, $orderId, $userId, $ticketNumber, new \DateTimeImmutable(), $total);
    }

    public function getUuid(): Uuid { return $this->uuid; }
    public function getRestaurantId(): int { return $this->restaurantId; }
    public function getOrderId(): Uuid { return $this->orderId; }
    public function getUserId(): Uuid { return $this->userId; }
    public function getTicketNumber(): int { return $this->ticketNumber; }
    public function getValueDate(): \DateTimeImmutable { return $this->valueDate; }
    public function getTotal(): int { return $this->total; }
}