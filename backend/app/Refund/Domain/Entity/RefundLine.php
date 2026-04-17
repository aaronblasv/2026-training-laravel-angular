<?php

declare(strict_types=1);

namespace App\Refund\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;

class RefundLine
{
    private function __construct(
        private Uuid $uuid,
        private Uuid $refundId,
        private Uuid $saleLineId,
        private int $quantity,
        private int $subtotal,
        private int $taxAmount,
        private int $total,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        Uuid $refundId,
        Uuid $saleLineId,
        int $quantity,
        int $subtotal,
        int $taxAmount,
        int $total,
    ): self {
        return new self($uuid, $refundId, $saleLineId, $quantity, $subtotal, $taxAmount, $total);
    }

    public function uuid(): Uuid { return $this->uuid; }
    public function refundId(): Uuid { return $this->refundId; }
    public function saleLineId(): Uuid { return $this->saleLineId; }
    public function quantity(): int { return $this->quantity; }
    public function subtotal(): int { return $this->subtotal; }
    public function taxAmount(): int { return $this->taxAmount; }
    public function total(): int { return $this->total; }
}