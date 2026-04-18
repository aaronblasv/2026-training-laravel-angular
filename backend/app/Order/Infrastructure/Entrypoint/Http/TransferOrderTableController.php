<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\TransferOrderTable\TransferOrderTable;
use App\Shared\Infrastructure\Http\DispatchesActionLogged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferOrderTableController
{
    use DispatchesActionLogged;

    public function __construct(
        private TransferOrderTable $useCase,
    ) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $validated = $request->validate([
            'target_table_id' => 'required|string',
        ]);

        ($this->useCase)(
            $orderUuid,
            $validated['target_table_id'],
            $request->user()->restaurant_id,
        );

        $this->logAction(
            $request->user()->restaurant_id,
            $request->user()->uuid,
            'order.transferred',
            'order',
            $orderUuid,
            [
                'target_table_id' => $validated['target_table_id'],
            ],
            $request->ip(),
        );

        return new JsonResponse(null, 204);
    }
}