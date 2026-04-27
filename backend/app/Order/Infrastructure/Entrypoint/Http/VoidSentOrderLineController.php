<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\VoidSentOrderLine\VoidSentOrderLine;
use App\Shared\Application\Context\AuditContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoidSentOrderLineController
{
    public function __construct(private VoidSentOrderLine $useCase) {}

    public function __invoke(Request $request, string $orderUuid, string $lineUuid): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        ($this->useCase)(
            new AuditContext(
                (int) $request->user()->restaurant_id,
                $request->user()->uuid,
                $request->ip(),
            ),
            $orderUuid,
            $lineUuid,
            (int) ($validated['quantity'] ?? 1),
        );

        return new JsonResponse(null, 204);
    }
}
