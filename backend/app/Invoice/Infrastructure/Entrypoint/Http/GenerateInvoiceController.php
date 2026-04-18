<?php

declare(strict_types=1);

namespace App\Invoice\Infrastructure\Entrypoint\Http;

use App\Invoice\Application\GenerateInvoice\GenerateInvoice;
use App\Shared\Infrastructure\Http\DispatchesActionLogged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerateInvoiceController
{
    use DispatchesActionLogged;

    public function __construct(
        private GenerateInvoice $useCase,
    ) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $response = ($this->useCase)($orderUuid, $request->user()->restaurant_id);

        $this->logAction(
            $request->user()->restaurant_id,
            $request->user()->uuid,
            'invoice.generated',
            'invoice',
            $response->uuid,
            [
                'order_uuid'     => $orderUuid,
                'invoice_number' => $response->invoiceNumber,
                'total'          => $response->total,
            ],
            $request->ip(),
        );

        return new JsonResponse($response->toArray(), 201);
    }
}
