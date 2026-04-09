<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\CancelOrder\CancelOrder;
use Illuminate\Http\JsonResponse;

class CancelOrderController
{
    public function __construct(private CancelOrder $useCase) {}

    public function __invoke(string $orderUuid): JsonResponse
    {
        ($this->useCase)($orderUuid);

        return new JsonResponse(null, 204);
    }
}