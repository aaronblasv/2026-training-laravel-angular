<?php

declare(strict_types=1);

namespace App\CashShift\Infrastructure\Entrypoint\Http;

use App\CashShift\Application\OpenCashShift\OpenCashShift;
use App\Shared\Application\Context\AuditContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OpenCashShiftController
{
    public function __construct(
        private OpenCashShift $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        if (!in_array($request->user()->role, ['admin', 'supervisor'], true)) {
            throw new AccessDeniedHttpException('Solo un administrador o supervisor puede abrir la caja.');
        }

        $validated = $request->validate([
            'opening_cash' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $response = ($this->useCase)(
            new AuditContext(
                $request->user()->restaurant_id,
                $request->user()->uuid,
                $request->ip(),
            ),
            $validated['opening_cash'],
            $validated['notes'] ?? null,
        );

        return new JsonResponse($response, 201);
    }
}