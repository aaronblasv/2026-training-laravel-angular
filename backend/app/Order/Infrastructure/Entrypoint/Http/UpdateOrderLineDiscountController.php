<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\UpdateOrderLineDiscount\UpdateOrderLineDiscount;
use App\Shared\Application\Context\AuditContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateOrderLineDiscountController
{
    public function __construct(
        private UpdateOrderLineDiscount $useCase,
    ) {}

    public function __invoke(Request $request, string $orderUuid, string $lineUuid): JsonResponse
    {
        $validated = $request->validate([
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_value' => 'nullable|integer|min:0',
        ]);

        $discountType = $validated['discount_type'] ?? null;
        $discountValue = (int) ($validated['discount_value'] ?? 0);

        ($this->useCase)(
            new AuditContext(
                $request->user()->restaurant_id,
                $request->user()->uuid,
                $request->ip(),
            ),
            $orderUuid,
            $lineUuid,
            $discountType,
            $discountValue,
        );

        return new JsonResponse(null, 204);
    }
}