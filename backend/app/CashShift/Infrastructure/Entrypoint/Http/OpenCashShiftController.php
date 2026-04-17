<?php

declare(strict_types=1);

namespace App\CashShift\Infrastructure\Entrypoint\Http;

use App\CashShift\Application\OpenCashShift\OpenCashShift;
use App\Log\Application\CreateLog\CreateLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenCashShiftController
{
    public function __construct(
        private OpenCashShift $useCase,
        private CreateLog $createLog,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opening_cash' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $response = ($this->useCase)(
            $request->user()->restaurant_id,
            $request->user()->uuid,
            $validated['opening_cash'],
            $validated['notes'] ?? null,
        );

        ($this->createLog)(
            $request->user()->restaurant_id,
            $request->user()->uuid,
            'cash_shift.opened',
            'cash_shift',
            $response['uuid'],
            $response,
            $request->ip(),
        );

        return new JsonResponse($response, 201);
    }
}