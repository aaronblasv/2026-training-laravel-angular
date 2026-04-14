<?php

namespace App\Payment\Domain\Interfaces;

use App\Payment\Domain\Entity\Payment;

interface PaymentRepositoryInterface
{
    public function save(Payment $payment): void;

    public function findByOrderId(string $orderUuid): array;

    public function getTotalPaidByOrder(string $orderUuid): int;
}
