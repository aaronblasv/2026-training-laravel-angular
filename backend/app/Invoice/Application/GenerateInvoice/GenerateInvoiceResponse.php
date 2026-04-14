<?php

namespace App\Invoice\Application\GenerateInvoice;

use App\Invoice\Domain\Entity\Invoice;

final readonly class GenerateInvoiceResponse
{
    private function __construct(
        public string $uuid,
        public string $invoiceNumber,
        public int $subtotal,
        public int $taxAmount,
        public int $total,
        public string $issuedAt,
    ) {}

    public static function create(Invoice $invoice): self
    {
        return new self(
            $invoice->uuid()->getValue(),
            $invoice->invoiceNumber(),
            $invoice->subtotal(),
            $invoice->taxAmount(),
            $invoice->total(),
            $invoice->issuedAt()->format('Y-m-d H:i:s'),
        );
    }
}
