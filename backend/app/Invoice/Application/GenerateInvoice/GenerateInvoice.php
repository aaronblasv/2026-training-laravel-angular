<?php

declare(strict_types=1);

namespace App\Invoice\Application\GenerateInvoice;

use App\Invoice\Domain\Entity\Invoice;
use App\Invoice\Domain\Interfaces\InvoiceOrderDataProviderInterface;
use App\Invoice\Domain\Interfaces\InvoiceRepositoryInterface;
use App\Shared\Domain\ValueObject\Uuid;
use Illuminate\Support\Facades\DB;

class GenerateInvoice
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceOrderDataProviderInterface $orderDataProvider,
    ) {}

    public function __invoke(string $orderUuid, int $restaurantId): GenerateInvoiceResponse
    {
        return DB::transaction(function () use ($orderUuid, $restaurantId) {
            $orderData = $this->orderDataProvider->getOrderForInvoice($orderUuid, $restaurantId);

            if (!$orderData) {
                throw new \DomainException("Order not found: {$orderUuid}");
            }

            $invoiceNumber = $this->invoiceRepository->getNextInvoiceNumber();

            $invoice = Invoice::dddCreate(
                Uuid::generate(),
                Uuid::create($orderData->orderUuid),
                $invoiceNumber,
                $orderData->subtotal,
                $orderData->taxAmount,
                $orderData->total,
            );

            $this->invoiceRepository->save($invoice);

            return GenerateInvoiceResponse::create($invoice);
        });
    }
}
