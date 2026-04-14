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

    public static function fromPersistence(
        string $uuid,
        int $restaurantId,
        string $orderId,
        string $userId,
        int $ticketNumber,
        \DateTimeImmutable $valueDate,
        int $total,
    ): self {
        return new self(
            Uuid::create($uuid),
            $restaurantId,
            Uuid::create($orderId),
            Uuid::create($userId),
            $ticketNumber,
            $valueDate,
            $total,
        );
    }

    public function uuid(): Uuid { return $this->uuid; }
    public function restaurantId(): int { return $this->restaurantId; }
    public function orderId(): Uuid { return $this->orderId; }
    public function userId(): Uuid { return $this->userId; }
    public function ticketNumber(): int { return $this->ticketNumber; }
    public function valueDate(): \DateTimeImmutable { return $this->valueDate; }
    public function total(): int { return $this->total; }
}