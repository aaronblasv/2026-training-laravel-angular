<?php

declare(strict_types=1);

namespace App\Refund\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;

class Refund
{
    private function __construct(
        private Uuid $uuid,
        private int $restaurantId,
        private Uuid $saleId,
        private Uuid $userId,
        private string $type,
        private string $method,
        private ?string $reason,
        private int $subtotal,
        private int $taxAmount,
        private int $total,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        int $restaurantId,
        Uuid $saleId,
        Uuid $userId,
        string $type,
        string $method,
        ?string $reason,
        int $subtotal,
        int $taxAmount,
        int $total,
    ): self {
        return new self($uuid, $restaurantId, $saleId, $userId, $type, $method, $reason, $subtotal, $taxAmount, $total);
    }

    public function uuid(): Uuid { return $this->uuid; }
    public function restaurantId(): int { return $this->restaurantId; }
    public function saleId(): Uuid { return $this->saleId; }
    public function userId(): Uuid { return $this->userId; }
    public function type(): string { return $this->type; }
    public function method(): string { return $this->method; }
    public function reason(): ?string { return $this->reason; }
    public function subtotal(): int { return $this->subtotal; }
    public function taxAmount(): int { return $this->taxAmount; }
    public function total(): int { return $this->total; }
}