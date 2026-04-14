<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
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
        private DomainDateTime $openedAt,
        private ?DomainDateTime $closedAt,
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
            DomainDateTime::now(),
            null,
        );
    }

    public static function fromPersistence(
        string $uuid,
        int $restaurantId,
        string $status,
        string $tableId,
        string $openedByUserId,
        ?string $closedByUserId,
        int $diners,
        \DateTimeImmutable $openedAt,
        ?\DateTimeImmutable $closedAt,
    ): self {
        return new self(
            Uuid::create($uuid),
            $restaurantId,
            OrderStatus::create($status),
            Uuid::create($tableId),
            Uuid::create($openedByUserId),
            $closedByUserId ? Uuid::create($closedByUserId) : null,
            Diners::create($diners),
            DomainDateTime::create($openedAt),
            $closedAt ? DomainDateTime::create($closedAt) : null,
        );
    }

    public function close(Uuid $closedByUserId): void
    {
        if (!$this->status->isOpen()) {
            throw new \DomainException('Cannot close an order that is not open.');
        }
        $this->status = OrderStatus::closed();
        $this->closedByUserId = $closedByUserId;
        $this->closedAt = DomainDateTime::now();
    }

    public function updateDiners(Diners $diners): void
    {
        $this->diners = $diners;
    }

    public function uuid(): Uuid { return $this->uuid; }
    public function restaurantId(): int { return $this->restaurantId; }
    public function status(): OrderStatus { return $this->status; }
    public function tableId(): Uuid { return $this->tableId; }
    public function openedByUserId(): Uuid { return $this->openedByUserId; }
    public function closedByUserId(): ?Uuid { return $this->closedByUserId; }
    public function diners(): Diners { return $this->diners; }
    public function openedAt(): DomainDateTime { return $this->openedAt; }
    public function closedAt(): ?DomainDateTime { return $this->closedAt; }

    public function calculateSubtotal(array $lines): int
    {
        return array_reduce($lines, function ($carry, $line) {
            return $carry + ($line->price() * $line->quantity()->getValue());
        }, 0);
    }

    public function calculateTaxAmount(array $lines): int
    {
        return array_reduce($lines, function ($carry, $line) {
            $lineSubtotal = $line->price() * $line->quantity()->getValue();
            $taxAmount = (int) round($lineSubtotal * $line->taxPercentage() / 100);
            return $carry + $taxAmount;
        }, 0);
    }
}