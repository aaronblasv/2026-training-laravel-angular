<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\CloseOrder\CloseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CloseOrderController
{
    public function __construct(private CloseOrder $useCase) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $validated = $request->validate([
            'closed_by_user_id' => 'required|string',
        ]);

        $response = ($this->useCase)($orderUuid, $validated['closed_by_user_id']);

        return new JsonResponse($response);
    }
}