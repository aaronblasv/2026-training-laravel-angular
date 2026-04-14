<?php

namespace App\Payment\Domain\Entity;

use App\Shared\Domain\ValueObject\Uuid;

class Payment
{
    private function __construct(
        private Uuid $uuid,
        private Uuid $orderId,
        private Uuid $userId,
        private int $amount,
        private string $method, // 'cash', 'card', 'bizum'
        private ?string $description = null,
    ) {}

    public static function dddCreate(
        Uuid $uuid,
        Uuid $orderId,
        Uuid $userId,
        int $amount,
        string $method,
        ?string $description = null,
    ): self {
        return new self($uuid, $orderId, $userId, $amount, $method, $description);
    }

    public static function fromPersistence(
        string $uuid,
        string $orderId,
        string $userId,
        int $amount,
        string $method,
        ?string $description = null,
    ): self {
        return new self(
            Uuid::create($uuid),
            Uuid::create($orderId),
            Uuid::create($userId),
            $amount,
            $method,
            $description,
        );
    }

    public function uuid(): Uuid { return $this->uuid; }
    public function orderId(): Uuid { return $this->orderId; }
    public function userId(): Uuid { return $this->userId; }
    public function amount(): int { return $this->amount; }
    public function method(): string { return $this->method; }
    public function description(): ?string { return $this->description; }
}
