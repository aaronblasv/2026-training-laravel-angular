<?php

declare(strict_types=1);

namespace App\Order\Application\GetOrderByTable;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Entity\OrderLine;

final readonly class GetOrderByTableResponse
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
        public array $lines,
    ) {}

    public static function create(Order $order, array $lines): self
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
            array_map(fn(OrderLine $line) => [
                'uuid'          => $line->uuid()->getValue(),
                'productId'     => $line->productId()->getValue(),
                'userId'        => $line->userId()->getValue(),
                'quantity'      => $line->quantity()->getValue(),
                'price'         => $line->price(),
                'taxPercentage' => $line->taxPercentage(),
                'discountType'  => $line->discountType(),
                'discountValue' => $line->discountValue(),
                'discountAmount' => $line->discountAmount(),
            ], $lines),
        );
    }

    /**
     * @return array<string, mixed>
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
            'lines' => array_map(static fn(array $line) => [
                'uuid' => $line['uuid'] ?? null,
                'product_id' => $line['productId'] ?? null,
                'user_id' => $line['userId'] ?? null,
                'quantity' => $line['quantity'] ?? null,
                'price' => $line['price'] ?? null,
                'tax_percentage' => $line['taxPercentage'] ?? null,
                'discount_type' => $line['discountType'] ?? null,
                'discount_value' => $line['discountValue'] ?? 0,
                'discount_amount' => $line['discountAmount'] ?? 0,
            ], $this->lines),
        ];
    }
}