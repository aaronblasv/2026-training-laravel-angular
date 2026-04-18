<?php

declare(strict_types=1);

namespace App\Payment\Infrastructure\Persistence\Repositories;

use App\Payment\Domain\Entity\Payment;
use App\Payment\Domain\Interfaces\PaymentRepositoryInterface;
use App\Payment\Infrastructure\Persistence\Models\EloquentPayment;
use App\Order\Infrastructure\Persistence\Models\EloquentOrder;
use App\User\Infrastructure\Persistence\Models\EloquentUser;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(
        private EloquentPayment $model,
        private EloquentOrder $orderModel,
        private EloquentUser $userModel,
    ) {}

    public function save(Payment $payment): void
    {
        $order = $this->orderModel->newQuery()->where('uuid', $payment->orderId()->getValue())->first();
        $user = $this->userModel->newQuery()->where('uuid', $payment->userId()->getValue())->first();

        if (!$order) {
            throw new \DomainException('Order not found');
        }

        if (!$user) {
            throw new \DomainException('User not found');
        }

        $this->model->newQuery()->create([
            'uuid'        => $payment->uuid()->getValue(),
            'order_id'    => $order->id,
            'user_id'     => $user->id,
            'amount'      => $payment->amount(),
            'method'      => $payment->method(),
            'description' => $payment->description(),
        ]);
    }

    public function findByOrderId(string $orderUuid): array
    {
        $order = $this->orderModel->newQuery()->where('uuid', $orderUuid)->first();

        if (!$order) {
            return [];
        }

        return $this->model->newQuery()
            ->with(['order', 'user'])
            ->where('order_id', $order->id)
            ->get()
            ->map(fn(EloquentPayment $payment) => $this->toDomain($payment))
            ->toArray();
    }

    public function getTotalPaidByOrder(string $orderUuid): int
    {
        $order = $this->orderModel->newQuery()->where('uuid', $orderUuid)->first();

        if (!$order) {
            return 0;
        }

        return $this->model->newQuery()->where('order_id', $order->id)->sum('amount') ?? 0;
    }

    private function toDomain(EloquentPayment $payment): Payment
    {
        $orderUuid = $payment->relationLoaded('order')
            ? $payment->order->uuid
            : $this->orderModel->newQuery()->find($payment->order_id)->uuid;
        $userUuid = $payment->relationLoaded('user')
            ? $payment->user->uuid
            : $this->userModel->newQuery()->find($payment->user_id)->uuid;

        return Payment::fromPersistence(
            $payment->uuid,
            $orderUuid,
            $userUuid,
            $payment->amount,
            $payment->method,
            $payment->description,
        );
    }
}
