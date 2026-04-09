<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Order\Domain\ValueObject\Diners;

class Order
{
    private function __construct(
        private Uuid $uuid,
        private int $restaurantId,
        private OrderStatus $status,
        private Uuid $tableId,
        private Uuid $openedByUserId,
        private ?Uuid $closedByUserId,
        private Diners $diners,
        private \DateTimeImmutable $openedAt,
        private ?\DateTimeImmutable $closedAt,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        int $restaurantId,
        Uuid $tableId,
        Uuid $openedByUserId,
        Diners $diners,
    ): self {
        return new self(
            $uuid,
            $restaurantId,
            OrderStatus::open(),
            $tableId,
            $openedByUserId,
            null,
            $diners,
            new \DateTimeImmutable(),
            null,
        );
    }

    public static function dddRestore(
        Uuid $uuid,
        int $restaurantId,
        OrderStatus $status,
        Uuid $tableId,
        Uuid $openedByUserId,
        ?Uuid $closedByUserId,
        Diners $diners,
        \DateTimeImmutable $openedAt,
        ?\DateTimeImmutable $closedAt,
    ): self {
        return new self($uuid, $restaurantId, $status, $tableId, $openedByUserId, $closedByUserId, $diners, $openedAt, $closedAt);
    }

    public function close(Uuid $closedByUserId): void
    {
        if (!$this->status->isOpen()) {
            throw new \DomainException('Cannot close an order that is not open.');
        }
        $this->status = OrderStatus::closed();
        $this->closedByUserId = $closedByUserId;
        $this->closedAt = new \DateTimeImmutable();
    }

    public function updateDiners(Diners $diners): void
    {
        $this->diners = $diners;
    }

    public function getUuid(): Uuid { return $this->uuid; }
    public function getRestaurantId(): int { return $this->restaurantId; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getTableId(): Uuid { return $this->tableId; }
    public function getOpenedByUserId(): Uuid { return $this->openedByUserId; }
    public function getClosedByUserId(): ?Uuid { return $this->closedByUserId; }
    public function getDiners(): Diners { return $this->diners; }
    public function getOpenedAt(): \DateTimeImmutable { return $this->openedAt; }
    public function getClosedAt(): ?\DateTimeImmutable { return $this->closedAt; }
}