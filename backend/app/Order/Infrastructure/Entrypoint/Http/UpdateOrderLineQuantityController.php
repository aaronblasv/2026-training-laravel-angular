<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\UpdateOrderLineQuantity\UpdateOrderLineQuantity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateOrderLineQuantityController
{
    public function __construct(private UpdateOrderLineQuantity $useCase) {}

    public function __invoke(Request $request, string $orderUuid, string $lineUuid): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        ($this->useCase)($lineUuid, $validated['quantity'], $request->user()->restaurant_id);

        return new JsonResponse(null, 204);
    }
}
