<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\UpdateOrderDiners\UpdateOrderDiners;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateOrderDinersController
{
    public function __construct(private UpdateOrderDiners $useCase) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $validated = $request->validate([
            'diners' => 'required|integer|min:1',
        ]);

        ($this->useCase)($orderUuid, $validated['diners']);

        return new JsonResponse(null, 204);
    }
}