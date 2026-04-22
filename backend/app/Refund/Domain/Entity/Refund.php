<?php

declare(strict_types=1);

namespace App\Refund\Domain\Entity;

use App\Payment\Domain\Exception\InvalidPaymentMethodException;
use App\Payment\Domain\ValueObject\PaymentMethod;
use App\Shared\Domain\Interfaces\HasDomainEventsInterface;
use App\Shared\Domain\Support\RecordsDomainEvents;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\Uuid;

class Refund implements HasDomainEventsInterface
{
    use RecordsDomainEvents;

    private function __construct(
        private Uuid $uuid,
        private RestaurantId $restaurantId,
        private Uuid $saleId,
        private Uuid $userId,
        private string $type,
        private PaymentMethod $method,
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
        return new self($uuid, RestaurantId::create($restaurantId), $saleId, $userId, $type, self::resolveMethod($method), $reason, $subtotal, $taxAmount, $total);
    }

    public function id(): Uuid { return $this->uuid; }
    public function uuid(): Uuid { return $this->id(); }
    public function restaurantId(): int { return $this->restaurantId->getValue(); }
    public function saleId(): Uuid { return $this->saleId; }
    public function userId(): Uuid { return $this->userId; }
    public function type(): string { return $this->type; }
    public function method(): string { return $this->method->value; }
    public function reason(): ?string { return $this->reason; }
    public function subtotal(): int { return $this->subtotal; }
    public function taxAmount(): int { return $this->taxAmount; }
    public function total(): int { return $this->total; }

    private static function resolveMethod(string $method): PaymentMethod
    {
        return PaymentMethod::tryFrom($method) ?? throw new InvalidPaymentMethodException($method);
    }
}