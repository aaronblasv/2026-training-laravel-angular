<?php

declare(strict_types=1);

namespace App\Payment\Application\RegisterPayment;

use App\Payment\Domain\Entity\Payment;
use App\Payment\Domain\Interfaces\PaymentRepositoryInterface;
use App\Shared\Application\Context\AuditContext;
use App\Shared\Domain\Event\ActionLogged;
use App\Shared\Domain\Interfaces\DomainEventBusInterface;
use App\Shared\Domain\Interfaces\TransactionManagerInterface;
use App\Shared\Domain\ValueObject\Uuid;

class RegisterPayment
{
    public function __construct(
        private PaymentRepositoryInterface $repository,
        private TransactionManagerInterface $transactionManager,
        private DomainEventBusInterface $domainEventBus,
    ) {}

    public function __invoke(
        AuditContext $auditContext,
        string $orderUuid,
        int $amount,
        string $method,
        ?string $description = null,
    ): RegisterPaymentResponse {
        return $this->transactionManager->run(function () use ($auditContext, $orderUuid, $amount, $method, $description) {
            $payment = Payment::dddCreate(
                Uuid::generate(),
                Uuid::create($orderUuid),
                Uuid::create($auditContext->userId),
                $amount,
                $method,
                $description,
            );

            $this->repository->save($payment);

            $totalPaid = $this->repository->getTotalPaidByOrder($orderUuid);
            $response = RegisterPaymentResponse::create($payment, $totalPaid);

            $payment->recordDomainEvent(ActionLogged::create(
                $auditContext->restaurantId,
                $auditContext->userId,
                'payment.registered',
                'order',
                $orderUuid,
                [
                    'amount' => $amount,
                    'method' => $method,
                    'total_paid' => $response->totalPaid,
                ],
                $auditContext->ipAddress,
            ));

            $this->domainEventBus->dispatch(...$payment->pullDomainEvents());

            return $response;
        });
    }
}
