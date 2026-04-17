<?php

declare(strict_types=1);

namespace App\Order\Application\OpenOrder;

use App\Order\Domain\Entity\Order;

final readonly class OpenOrderResponse
{
    private function __construct(
        public string $uuid,
        public string $status,
        public string $tableId,
        public string $openedByUserId,
        public int $diners,
        public ?string $discountType,
        public int $discountValue,
        public int $discountAmount,
        public string $openedAt,
    ) {}

    public static function create(Order $order): self
    {
        return new self(
            $order->uuid()->getValue(),
            $order->status()->getValue(),
            $order->tableId()->getValue(),
            $order->openedByUserId()->getValue(),
            $order->diners()->getValue(),
            $order->discountType(),
            $order->discountValue(),
            $order->discountAmount(),
            $order->openedAt()->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status,
            'table_id' => $this->tableId,
            'opened_by_user_id' => $this->openedByUserId,
            'diners' => $this->diners,
            'discount_type' => $this->discountType,
            'discount_value' => $this->discountValue,
            'discount_amount' => $this->discountAmount,
            'opened_at' => $this->openedAt,
        ];
    }
}