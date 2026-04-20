<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\OpenOrder\OpenOrder;
use App\Shared\Application\Context\AuditContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenOrderController
{
    public function __construct(
        private OpenOrder $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|uuid',
            'opened_by_user_id' => 'required|uuid',
            'diners' => 'required|integer|min:1',
        ]);

        $response = ($this->useCase)(
            new AuditContext(
                $request->user()->restaurant_id,
                $request->user()->uuid,
                $request->ip(),
            ),
            $validated['table_id'],
            $validated['opened_by_user_id'],
            $validated['diners'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}