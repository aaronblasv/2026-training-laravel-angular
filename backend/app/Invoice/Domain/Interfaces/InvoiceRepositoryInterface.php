<?php

declare(strict_types=1);

namespace App\Invoice\Domain\Interfaces;

use App\Invoice\Domain\Entity\Invoice;

interface InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void;

    public function findByOrderId(string $orderUuid): ?Invoice;

    public function getNextInvoiceNumber(): string;
}
