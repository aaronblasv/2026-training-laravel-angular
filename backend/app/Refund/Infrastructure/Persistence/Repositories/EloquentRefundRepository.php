<?php

declare(strict_types=1);

namespace App\Refund\Infrastructure\Persistence\Repositories;

use App\Refund\Domain\Entity\Refund;
use App\Refund\Domain\Entity\RefundLine;
use App\Refund\Domain\Exception\RefundNotFoundException;
use App\Refund\Domain\Interfaces\RefundRepositoryInterface;
use App\Refund\Infrastructure\Persistence\Models\EloquentRefund;
use App\Refund\Infrastructure\Persistence\Models\EloquentRefundLine;
use App\Sale\Domain\Exception\SaleLineNotFoundException;
use App\Sale\Domain\Exception\SaleNotFoundException;
use App\Sale\Infrastructure\Persistence\Models\EloquentSale;
use App\Sale\Infrastructure\Persistence\Models\EloquentSaleLine;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $restaurantId = $refund->restaurantId();
        $saleId = $this->resolveSaleId($refund->saleId()->getValue(), $restaurantId);
        $userId = $this->resolveUserId($refund->userId()->getValue(), $restaurantId);

        $this->model->newQuery()->create([
            'uuid' => $refund->uuid()->getValue(),
            'restaurant_id' => $restaurantId,
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
        $refund = $this->resolveRefund($line->refundId()->getValue());
        $saleLineId = $this->resolveSaleLineId($line->saleLineId()->getValue(), (int) $refund->restaurant_id);

        $this->lineModel->newQuery()->create([
            'uuid' => $line->uuid()->getValue(),
            'refund_id' => $refund->id,
            'sale_line_id' => $saleLineId,
            'quantity' => $line->quantity(),
            'subtotal' => $line->subtotal(),
            'tax_amount' => $line->taxAmount(),
            'total' => $line->total(),
        ]);
    }

    public function saveLinesBatch(array $lines): void
    {
        if ($lines === []) {
            return;
        }

        /** @var RefundLine $firstLine */
        $firstLine = $lines[0];
        $refund = $this->resolveRefund($firstLine->refundId()->getValue());
        $restaurantId = (int) $refund->restaurant_id;

        $saleLineIds = $this->saleLineModel->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->whereIn('uuid', array_map(static fn (RefundLine $line): string => $line->saleLineId()->getValue(), $lines))
            ->pluck('id', 'uuid')
            ->all();

        $timestamp = now();
        $rows = [];

        foreach ($lines as $line) {
            if (! isset($saleLineIds[$line->saleLineId()->getValue()])) {
                throw new SaleLineNotFoundException($line->saleLineId()->getValue());
            }

            $rows[] = [
                'uuid' => $line->uuid()->getValue(),
                'refund_id' => $refund->id,
                'sale_line_id' => $saleLineIds[$line->saleLineId()->getValue()],
                'quantity' => $line->quantity(),
                'subtotal' => $line->subtotal(),
                'tax_amount' => $line->taxAmount(),
                'total' => $line->total(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $this->lineModel->newQuery()->insert($rows);
    }

    private function resolveSaleId(string $saleUuid, int $restaurantId): int
    {
        try {
            return $this->saleModel->newQuery()
                ->where('uuid', $saleUuid)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail()
                ->id;
        } catch (ModelNotFoundException) {
            throw new SaleNotFoundException($saleUuid);
        }
    }

    private function resolveUserId(string $userUuid, int $restaurantId): int
    {
        try {
            return $this->userModel->newQuery()
                ->where('uuid', $userUuid)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail()
                ->id;
        } catch (ModelNotFoundException) {
            throw new UserNotFoundException($userUuid);
        }
    }

    private function resolveRefund(string $refundUuid): EloquentRefund
    {
        try {
            return $this->model->newQuery()->where('uuid', $refundUuid)->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new RefundNotFoundException($refundUuid);
        }
    }

    private function resolveSaleLineId(string $saleLineUuid, int $restaurantId): int
    {
        try {
            return $this->saleLineModel->newQuery()
                ->where('uuid', $saleLineUuid)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail()
                ->id;
        } catch (ModelNotFoundException) {
            throw new SaleLineNotFoundException($saleLineUuid);
        }
    }
}
