<?php

declare(strict_types=1);

namespace App\CashShift\Infrastructure\Persistence\Repositories;

use App\CashShift\Domain\Entity\CashShift;
use App\CashShift\Domain\Interfaces\CashShiftRepositoryInterface;
use App\CashShift\Infrastructure\Persistence\Models\EloquentCashShift;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Support\Facades\DB;

class EloquentCashShiftRepository implements CashShiftRepositoryInterface
{
    public function __construct(
        private EloquentCashShift $model,
        private EloquentUser $userModel,
    ) {}

    public function save(CashShift $cashShift): void
    {
        $openedByUserId = $this->userModel->newQuery()->where('uuid', $cashShift->openedByUserId()->getValue())->firstOrFail()->id;

        $this->model->newQuery()->create([
            'uuid' => $cashShift->uuid()->getValue(),
            'restaurant_id' => $cashShift->restaurantId(),
            'opened_by_user_id' => $openedByUserId,
            'closed_by_user_id' => null,
            'status' => $cashShift->status(),
            'opening_cash' => $cashShift->openingCash(),
            'cash_total' => $cashShift->cashTotal(),
            'card_total' => $cashShift->cardTotal(),
            'bizum_total' => $cashShift->bizumTotal(),
            'refund_total' => $cashShift->refundTotal(),
            'counted_cash' => $cashShift->countedCash(),
            'cash_difference' => $cashShift->cashDifference(),
            'notes' => $cashShift->notes(),
            'opened_at' => $cashShift->openedAt()->format('Y-m-d H:i:s'),
            'closed_at' => null,
        ]);
    }

    public function update(CashShift $cashShift): void
    {
        $closedByUserId = $cashShift->closedByUserId()
            ? $this->userModel->newQuery()->where('uuid', $cashShift->closedByUserId()->getValue())->firstOrFail()->id
            : null;

        $this->model->newQuery()
            ->where('uuid', $cashShift->uuid()->getValue())
            ->firstOrFail()
            ->update([
                'closed_by_user_id' => $closedByUserId,
                'status' => $cashShift->status(),
                'cash_total' => $cashShift->cashTotal(),
                'card_total' => $cashShift->cardTotal(),
                'bizum_total' => $cashShift->bizumTotal(),
                'refund_total' => $cashShift->refundTotal(),
                'counted_cash' => $cashShift->countedCash(),
                'cash_difference' => $cashShift->cashDifference(),
                'notes' => $cashShift->notes(),
                'closed_at' => $cashShift->closedAt()?->format('Y-m-d H:i:s'),
            ]);
    }

    public function findOpenByRestaurant(int $restaurantId): ?CashShift
    {
        $model = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findByUuid(int $restaurantId, string $uuid): ?CashShift
    {
        $model = $this->model->newQuery()
            ->where('restaurant_id', $restaurantId)
            ->where('uuid', $uuid)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function getWindowSummary(int $restaurantId, \DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $fromString = $from->format('Y-m-d H:i:s');
        $toString = ($to ?? new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $paymentTotals = DB::table('payments as p')
            ->join('orders as o', 'p.order_id', '=', 'o.id')
            ->where('o.restaurant_id', $restaurantId)
            ->whereBetween('p.created_at', [$fromString, $toString])
            ->select(
                DB::raw("SUM(CASE WHEN p.method = 'cash' THEN p.amount ELSE 0 END) as cash_total"),
                DB::raw("SUM(CASE WHEN p.method = 'card' THEN p.amount ELSE 0 END) as card_total"),
                DB::raw("SUM(CASE WHEN p.method = 'bizum' THEN p.amount ELSE 0 END) as bizum_total"),
            )
            ->first();

        $refundTotals = DB::table('refunds')
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$fromString, $toString])
            ->select(
                DB::raw("SUM(CASE WHEN method = 'cash' THEN total ELSE 0 END) as cash_total"),
                DB::raw("SUM(CASE WHEN method = 'card' THEN total ELSE 0 END) as card_total"),
                DB::raw("SUM(CASE WHEN method = 'bizum' THEN total ELSE 0 END) as bizum_total"),
                DB::raw('SUM(total) as refund_total'),
            )
            ->first();

        $cashPayments = (int) ($paymentTotals->cash_total ?? 0);
        $cardPayments = (int) ($paymentTotals->card_total ?? 0);
        $bizumPayments = (int) ($paymentTotals->bizum_total ?? 0);
        $cashRefunds = (int) ($refundTotals->cash_total ?? 0);
        $cardRefunds = (int) ($refundTotals->card_total ?? 0);
        $bizumRefunds = (int) ($refundTotals->bizum_total ?? 0);

        return [
            'cash_total' => $cashPayments - $cashRefunds,
            'card_total' => $cardPayments - $cardRefunds,
            'bizum_total' => $bizumPayments - $bizumRefunds,
            'refund_total' => (int) ($refundTotals->refund_total ?? 0),
        ];
    }

    private function toDomain(EloquentCashShift $model): CashShift
    {
        $openedByUuid = $this->userModel->newQuery()->find($model->opened_by_user_id)->uuid;
        $closedByUuid = $model->closed_by_user_id
            ? $this->userModel->newQuery()->find($model->closed_by_user_id)->uuid
            : null;

        return CashShift::fromPersistence(
            $model->uuid,
            $model->restaurant_id,
            $openedByUuid,
            $closedByUuid,
            $model->status,
            (int) $model->opening_cash,
            (int) $model->cash_total,
            (int) $model->card_total,
            (int) $model->bizum_total,
            (int) $model->refund_total,
            $model->counted_cash !== null ? (int) $model->counted_cash : null,
            (int) $model->cash_difference,
            $model->notes,
            new \DateTimeImmutable($model->opened_at),
            $model->closed_at ? new \DateTimeImmutable($model->closed_at) : null,
        );
    }
}