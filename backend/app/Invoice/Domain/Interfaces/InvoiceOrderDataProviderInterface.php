<?php

declare(strict_types=1);

namespace App\Invoice\Domain\Interfaces;

use App\Invoice\Domain\ReadModel\OrderForInvoice;

interface InvoiceOrderDataProviderInterface
{
    public function getOrderForInvoice(string $orderUuid, int $restaurantId): ?OrderForInvoice;
}
