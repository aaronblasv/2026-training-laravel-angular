<?php

declare(strict_types=1);

namespace App\Refund\Infrastructure\Persistence\Repositories;

use App\Refund\Domain\Entity\Refund;
use App\Refund\Domain\Entity\RefundLine;
use App\Refund\Domain\Interfaces\RefundRepositoryInterface;
use App\Refund\Infrastructure\Persistence\Models\EloquentRefund;
use App\Refund\Infrastructure\Persistence\Models\EloquentRefundLine;
use App\Sale\Infrastructure\Persistence\Models\EloquentSale;
use App\Sale\Infrastructure\Persistence\Models\EloquentSaleLine;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

class EloquentRefundRepository implements RefundRepositoryInterface
{
    public function __construct(
        private EloquentRefund $model,
        private EloquentRefundLine $lineModel,
        private EloquentSale $saleModel,
        private EloquentSaleLine $saleLineModel,
        private EloquentUser $userModel,
    ) {}

    public function save(Refund $refund): void
    {
        $saleId = $this->saleModel->newQuery()->where('uuid', $refund->saleId()->getValue())->firstOrFail()->id;
        $userId = $this->userModel->newQuery()->where('uuid', $refund->userId()->getValue())->firstOrFail()->id;

        $this->model->newQuery()->create([
            'uuid' => $refund->uuid()->getValue(),
            'restaurant_id' => $refund->restaurantId(),
            'sale_id' => $saleId,
            'user_id' => $userId,
            'type' => $refund->type(),
            'method' => $refund->method(),
            'reason' => $refund->reason(),
            'subtotal' => $refund->subtotal(),
            'tax_amount' => $refund->taxAmount(),
            'total' => $refund->total(),
        ]);
    }

    public function saveLine(RefundLine $line): void
    {
        $refundId = $this->model->newQuery()->where('uuid', $line->refundId()->getValue())->firstOrFail()->id;
        $saleLineId = $this->saleLineModel->newQuery()->where('uuid', $line->saleLineId()->getValue())->firstOrFail()->id;

        $this->lineModel->newQuery()->create([
            'uuid' => $line->uuid()->getValue(),
            'refund_id' => $refundId,
            'sale_line_id' => $saleLineId,
            'quantity' => $line->quantity(),
            'subtotal' => $line->subtotal(),
            'tax_amount' => $line->taxAmount(),
            'total' => $line->total(),
        ]);
    }
}