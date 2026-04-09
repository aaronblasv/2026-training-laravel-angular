<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\RemoveOrderLine\RemoveOrderLine;
use Illuminate\Http\JsonResponse;

class RemoveOrderLineController
{
    public function __construct(private RemoveOrderLine $useCase) {}

    public function __invoke(string $orderUuid, string $lineUuid): JsonResponse
    {
        ($this->useCase)($lineUuid);

        return new JsonResponse(null, 204);
    }
}