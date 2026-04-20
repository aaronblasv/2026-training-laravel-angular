<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\TransferOrderTable\TransferOrderTable;
use App\Shared\Application\Context\AuditContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferOrderTableController
{
    public function __construct(
        private TransferOrderTable $useCase,
    ) {}

    public function __invoke(Request $request, string $orderUuid): JsonResponse
    {
        $validated = $request->validate([
            'target_table_id' => 'required|string',
        ]);

        ($this->useCase)(
            new AuditContext(
                $request->user()->restaurant_id,
                $request->user()->uuid,
                $request->ip(),
            ),
            $orderUuid,
            $validated['target_table_id'],
        );

        return new JsonResponse(null, 204);
    }
}