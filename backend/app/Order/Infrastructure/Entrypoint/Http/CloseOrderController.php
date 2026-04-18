<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\CloseOrder\CloseOrder;
use App\Shared\Infrastructure\Http\DispatchesActionLogged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CloseOrderController
{
    use DispatchesActionLogged;

    public function __construct(
        private CloseOrder $useCase,
    ) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $validated = $request->validate([
            'closed_by_user_id' => 'required|string',
        ]);

        $response = ($this->useCase)(
            $orderUuid,
            $validated['closed_by_user_id'],
            $request->user()->restaurant_id,
        );

        $this->logAction(
            $request->user()->restaurant_id,
            $request->user()->uuid,
            'order.closed',
            'order',
            $orderUuid,
            ['closed_by_user_id' => $validated['closed_by_user_id']],
            $request->ip(),
        );

        return new JsonResponse($response->toArray());
    }
}
