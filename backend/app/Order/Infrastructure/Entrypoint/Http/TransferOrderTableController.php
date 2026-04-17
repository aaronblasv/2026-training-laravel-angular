<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Log\Application\CreateLog\CreateLog;
use App\Order\Application\TransferOrderTable\TransferOrderTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferOrderTableController
{
    public function __construct(
        private TransferOrderTable $useCase,
        private CreateLog $createLog,
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

        ($this->createLog)(
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