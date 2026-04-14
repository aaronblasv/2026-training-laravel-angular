<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\CancelOrder\CancelOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CancelOrderController
{
    public function __construct(private CancelOrder $useCase) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        ($this->useCase)($orderUuid, $request->user()->restaurant_id);

        return new JsonResponse(null, 204);
    }
}
