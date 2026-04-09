<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\OpenOrder\OpenOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenOrderController
{
    public function __construct(private OpenOrder $useCase) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|string',
            'opened_by_user_id' => 'required|string',
            'diners' => 'required|integer|min:1',
        ]);

        $response = ($this->useCase)(
            auth()->user()->restaurant_id,
            $validated['table_id'],
            $validated['opened_by_user_id'],
            $validated['diners'],
        );

        return new JsonResponse($response, 201);
    }
}