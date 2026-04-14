<?php

namespace App\Invoice\Infrastructure\Entrypoint\Http;

use App\Invoice\Application\GenerateInvoice\GenerateInvoice;
use App\Log\Application\CreateLog\CreateLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GenerateInvoiceController
{
    public function __construct(
        private GenerateInvoice $useCase,
        private CreateLog $createLog,
    ) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        try {
            $response = ($this->useCase)($orderUuid, $request->user()->restaurant_id);

            ($this->createLog)(
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

            return new JsonResponse($response, 201);
        } catch (\DomainException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            \Log::error('Error generating invoice: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => 'Error generating invoice: ' . $e->getMessage()], 500);
        }
    }
}
